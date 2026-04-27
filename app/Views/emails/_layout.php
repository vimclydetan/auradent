<?php
/**
 * Base Email Layout - Auradent Dental Clinic
 * Usage: echo view('emails/_layout', ['content' => $content, 'data' => $payload]);
 */
$clinicName = 'Auradent Dental Clinic';
$clinicAddress = '123 Wellness Street, Makati City, Philippines';
$clinicPhone = '(02) 8888-1234';
$clinicEmail = 'care@auradent.ph';
$clinicWebsite = base_url();
$logoUrl = base_url('assets/images/auradent-logo.png'); // Ensure this is absolute URL in production
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($subject ?? 'Auradent Notification') ?></title>
    <!--[if mso]>
    <noscript>
    <xml>
    <o:OfficeDocumentSettings>
    <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
    <style type="text/css">
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; }
        table { border-spacing: 0; }
        td { padding: 0; vertical-align: top; }
        a { color: #2563eb; text-decoration: none; }
        a:hover { color: #1d4ed8; }
        .btn { background-color: #2563eb; color: #ffffff !important; padding: 12px 24px; border-radius: 6px; font-weight: 600; display: inline-block; }
        .btn:hover { background-color: #1d4ed8; }
        .btn-secondary { background-color: #64748b; }
        .btn-secondary:hover { background-color: #475569; }
        .btn-danger { background-color: #dc2626; }
        .btn-danger:hover { background-color: #b91c1c; }
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .content-padding { padding: 20px !important; }
            .stack { display: block !important; width: 100% !important; }
            .text-center-mobile { text-align: center !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;">
    <center style="width:100%;table-layout:fixed;background-color:#f8fafc;padding:20px 0;">
        <table class="container" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin:0 auto;overflow:hidden;">
            <!-- Header -->
            <tr>
                <td style="background:linear-gradient(135deg,#1e293b 0%,#334155 100%);padding:24px;text-align:center;">
                    <?php if (!empty($logoUrl)): ?>
                    <img src="<?= $logoUrl ?>" alt="<?= esc($clinicName) ?>" width="180" style="max-width:180px;height:auto;display:block;margin:0 auto;" />
                    <?php else: ?>
                    <h1 style="color:#ffffff;margin:0;font-size:20px;font-weight:700;letter-spacing:0.5px;"><?= esc($clinicName) ?></h1>
                    <?php endif; ?>
                    <p style="color:#cbd5e1;margin:8px 0 0 0;font-size:13px;">Professional Dental Care You Can Trust</p>
                </td>
            </tr>
            
            <!-- Content -->
            <tr>
                <td class="content-padding" style="padding:32px 40px;">
                    <?= $content ?? '' ?>
                </td>
            </tr>
            
            <!-- Footer -->
            <tr>
                <td style="background-color:#f1f5f9;padding:24px 40px;border-top:1px solid #e2e8f0;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="stack" style="padding-bottom:16px;">
                                <p style="margin:0 0 8px 0;font-size:13px;color:#475569;font-weight:600;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" style="vertical-align:-2px;margin-right:6px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <?= esc($clinicAddress) ?>
                                </p>
                                <p style="margin:0 0 8px 0;font-size:13px;color:#475569;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" style="vertical-align:-2px;margin-right:6px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $clinicPhone) ?>" style="color:#475569;"><?= esc($clinicPhone) ?></a>
                                </p>
                                <p style="margin:0;font-size:13px;color:#475569;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" style="vertical-align:-2px;margin-right:6px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    <a href="mailto:<?= esc($clinicEmail) ?>" style="color:#475569;"><?= esc($clinicEmail) ?></a>
                                </p>
                            </td>
                            <td class="stack text-center-mobile" style="text-align:right;">
                                <p style="margin:0 0 8px 0;font-size:13px;color:#475569;">
                                    <a href="<?= esc($clinicWebsite) ?>" style="color:#2563eb;font-weight:600;">Visit Our Website</a>
                                </p>
                                <p style="margin:0;font-size:11px;color:#94a3b8;">
                                    &copy; <?= date('Y') ?> <?= esc($clinicName) ?><br/>All rights reserved.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <!-- Unsubscribe / Preferences (Optional) -->
        <p style="max-width:600px;margin:16px auto 0 auto;text-align:center;font-size:11px;color:#94a3b8;">
            This email was sent regarding your appointment at <?= esc($clinicName) ?>.<br/>
            <a href="<?= base_url('preferences') ?>" style="color:#94a3b9;text-decoration:underline;">Manage email preferences</a>
        </p>
    </center>
</body>
</html>