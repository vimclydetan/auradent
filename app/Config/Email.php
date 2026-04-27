<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    // ALL BLANK → Will use .env values
    // app/Config/Email.php - TEMPORARY TEST ONLY
    public string $protocol   = 'smtp';
    public string $SMTPHost   = 'smtp.gmail.com';
    public string $SMTPUser   = 'vimclydetan26@gmail.com';
    public string $SMTPPass   = 'pouw fxjp tkvj ywrg';  // Without quotes here
    public int $SMTPPort     = 587;
    public string $SMTPCrypto = 'tls';
    public string $fromEmail  = 'vimclydetan26@gmail.com';
    public string $fromName   = 'Auradent Dental Clinic';
    public string $mailType   = 'html';
    public string $charset    = 'UTF-8';
    public bool $validate    = false;

    // Keep these as defaults (not overridden by .env)
    public string $userAgent = 'CodeIgniter';
    public string $mailPath = '/usr/sbin/sendmail';
    public string $SMTPAuthMethod = 'login';
    public int $SMTPTimeout = 10;
    public bool $SMTPKeepAlive = false;
    public bool $wordWrap = true;
    public int $wrapChars = 76;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 0;
    public bool $DSN = false;
}
