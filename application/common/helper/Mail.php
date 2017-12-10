<?php

namespace app\common\helper;

use PHPMailer\PHPMailer\PHPMailer;
use think\Config;

class Mail
{
    const PRE_BODY = <<<EOF
<html>
    <head>
        <meta http-equiv="Content-Language" content="zh-cn">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body>
EOF;
    const AFT_BODY = <<<EOF
    </body>
</html>
EOF;
    private static $mailer = null;
    private static $errMsg = '';
    private static function mIns()
    {
        if (is_null(self::$mailer)) {
            $cfg = Config::get('mail');
            self::$mailer = new PHPMailer();
            self::$mailer->IsSMTP();
            self::$mailer->Port = $cfg['port'] ?? 25;
            self::$mailer->Host = $cfg['host'];
            self::$mailer->SMTPAuth = true;
            self::$mailer->Username = $cfg['username'] ?? '';
            self::$mailer->Password = $cfg['password'] ?? '';
            self::$mailer->From = $cfg['from'] ?? ($cfg['username'] ?? 'webmaster@kazamigk.com');
            self::$mailer->FromName = "Kazami Labs Mail Service";
            self::$mailer->CharSet = "UTF-8";
            self::$mailer->Encoding = "base64";
            if (isset($cfg['secure'])) {
                self::$mailer->SMTPSecure = $cfg['secure'];
            }
        }
        return self::$mailer;
    }

    public static function setFromName(string $formName)
    {
        self::mIns()->FromName = $formName;
    }

    public static function setSendTo(string $email, string $name = null)
    {
        if (is_null($name)) {
            $name = $email;
        }
        self::mIns()->AddAddress($email, $name);
    }

    public static function setSubject(string $subject)
    {
        $subject = base64_encode($subject);
        self::mIns()->Subject = "=?utf-8?B?{$subject}?=";
    }

    public static function setBody(string $html)
    {
        self::mIns()->IsHTML(true);
        self::mIns()->Body = self::PRE_BODY . $html . self::AFT_BODY;
        self::mIns()->AltBody = 'text/html';
    }

    public static function exec(): bool
    {
        try {
            self::mIns()->send();
        } catch (\Exception $e) {
            self::$errMsg = $e->getMessage();
            return false;
        }
        return true;
    }

    public static function getError(): string
    {
        return self::$errMsg;
    }
}
