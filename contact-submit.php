<?php
/**
 * Eudora Vault Academy: email-only interest form.
 * This endpoint receives UTF-8 POST fields and sends a quoted-printable
 * UTF-8 plain-text message via the hosting account's PHP mail() transport.
 * No form data is written to a website database.
 */
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');

function validUtf8(string $value): bool
{
    return preg_match('//u', $value) === 1;
}

function utf8Length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }
    if (function_exists('iconv_strlen')) {
        $length = iconv_strlen($value, 'UTF-8');
        if ($length !== false) {
            return $length;
        }
    }
    return strlen($value);
}

function encodeEmailSubject(string $value): string
{
    if (function_exists('mb_encode_mimeheader')) {
        return mb_encode_mimeheader($value, 'UTF-8', 'B', "\r\n");
    }
    if (function_exists('iconv_mime_encode')) {
        $header = iconv_mime_encode('Subject', $value, [
            'scheme' => 'B',
            'input-charset' => 'UTF-8',
            'output-charset' => 'UTF-8',
            'line-length' => 76,
            'line-break-chars' => "\r\n",
        ]);
        if (is_string($header) && str_starts_with($header, 'Subject: ')) {
            return substr($header, strlen('Subject: '));
        }
    }
    return 'EVA Interest Form';
}

function showSubmissionError(int $status, string $message): never
{
    http_response_code($status);
    $safeMessage = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Interest Form | Eudora Vault Academy</title><link rel="stylesheet" href="assets/css/site.css"></head>'
        . '<body><main class="section"><div class="container"><div class="note-card"><h1>We couldn\'t send your form.</h1>'
        . '<p>' . $safeMessage . '</p><p><a class="button button-primary" href="contact.html">Return to the form</a></p>'
        . '<p>If the problem continues, email <a href="mailto:eudoravaultacademy@gmail.com">eudoravaultacademy@gmail.com</a> '
        . 'or use the <a href="https://docs.google.com/forms/d/e/1FAIpQLSc_Cwu7smSAwkutnff5Gzulh0JP9F6SC_V14usA3UT2aR2HIA/viewform">Google interest form</a>.</p>'
        . '</div></div></main></body></html>';
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    showSubmissionError(405, 'Please submit the interest form from the EVA contact page.');
}

if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 20000) {
    showSubmissionError(413, 'The form contains too much text. Please shorten your message and try again.');
}

// Honeypot: silent success keeps automated bots from probing the filter.
if (!empty($_POST['company_website'])) {
    header('Location: thank-you.html', true, 303);
    exit;
}

$fields = [
    'Athlete Name' => ['athlete_name', 120],
    'Athlete Phone Number' => ['athlete_phone', 50],
    'Athlete Email' => ['athlete_email', 254],
    'School' => ['school', 120],
    'Graduation Year' => ['graduation_year', 40],
    'Pole Vault Experience' => ['vault_experience', 60],
    'Current Personal Best' => ['personal_best', 120],
    'Guardian Name' => ['guardian_name', 120],
    'Guardian Phone Number' => ['guardian_phone', 50],
    'Guardian Email' => ['guardian_email', 254],
    'Preferred Contact' => ['preferred_contact', 40],
    'Notes or Questions' => ['notes', 4000],
];
$required = [
    'Athlete Name', 'Athlete Phone Number', 'School', 'Graduation Year',
    'Current Personal Best', 'Guardian Name', 'Guardian Phone Number', 'Preferred Contact',
];
$values = [];

foreach ($fields as $name => [$key, $maxLength]) {
    $raw = $_POST[$key] ?? '';
    if (!is_string($raw) || !validUtf8($raw)) {
        showSubmissionError(400, 'One or more fields contain invalid text. Please review your entries.');
    }
    // Preserve the applicant's actual punctuation (e.g. 15’ or 15').
    // Normalize only line breaks and remove non-printing control characters.
    $value = str_replace(["\r\n", "\r"], "\n", $raw);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
    if ($value === null) {
        showSubmissionError(400, 'Please review your entries and try again.');
    }
    $value = trim($value);
    if (utf8Length($value) > $maxLength) {
        showSubmissionError(400, 'One or more fields are too long. Please shorten them and try again.');
    }
    $values[$name] = $value;
}

foreach ($required as $name) {
    if ($values[$name] === '') {
        showSubmissionError(400, 'Please complete all required fields before submitting.');
    }
}

foreach (['Athlete Email', 'Guardian Email'] as $name) {
    if ($values[$name] !== '' && !filter_var($values[$name], FILTER_VALIDATE_EMAIL)) {
        showSubmissionError(400, 'Please enter a valid email address, or leave optional email fields blank.');
    }
}

if (!in_array($values['Preferred Contact'], ['Athlete', 'Guardian', 'Both', 'Other'], true)) {
    showSubmissionError(400, 'Please select a valid contact preference.');
}
if (!in_array($values['Graduation Year'], ['2026', '2027', '2028', '2029', '2030', '2031', 'Other'], true)) {
    showSubmissionError(400, 'Please select a valid graduation year.');
}
if (!in_array($values['Pole Vault Experience'], ['', '< 1 year', '1–2 years', '2+ years'], true)) {
    showSubmissionError(400, 'Please select a valid experience level.');
}

// A header is never populated from an unvalidated address. Subject text is
// flattened and MIME-encoded so non-ASCII athlete names remain readable.
$athleteName = preg_replace('/\s+/u', ' ', $values['Athlete Name']);
if ($athleteName === null) {
    showSubmissionError(400, 'Please provide a valid athlete name.');
}
$subject = encodeEmailSubject($athleteName . ': EVA Interest Form');

$bodyLines = [
    'EUDORA VAULT ACADEMY — INTEREST FORM',
    '-----------------------------------',
    '',
];
foreach ($fields as $name => $_) {
    $bodyLines[] = $name . ': ' . ($values[$name] !== '' ? $values[$name] : '(not provided)');
}
$bodyLines[] = '';
$bodyLines[] = 'Submitted from eudoravaultacademy.com';

// Quoted-printable + UTF-8 content headers keep typographic apostrophes,
// curly quotes, and other non-ASCII characters intact in email clients.
$body = quoted_printable_encode(implode("\r\n", $bodyLines) . "\r\n");
$headers = [
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/plain; charset=UTF-8',
    'Content-Transfer-Encoding' => 'quoted-printable',
    'From' => 'Eudora Vault Academy <no-reply@eudoravaultacademy.com>',
];
$replyTo = $values['Guardian Email'] !== '' ? $values['Guardian Email'] : $values['Athlete Email'];
if ($replyTo !== '') {
    $headers['Reply-To'] = $replyTo;
}

if (!mail('eudoravaultacademy@gmail.com', $subject, $body, $headers)) {
    error_log('EVA interest form: mail() returned false.');
    showSubmissionError(500, 'The email service is temporarily unavailable. Please use our Google Form or contact EVA directly.');
}

header('Location: thank-you.html', true, 303);
exit;
