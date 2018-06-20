<?php

namespace Bot\Line;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package \Bot\Line
 * @license MIT
 * @since 0.0.1
 */
final class Exe
{
    /**
     * @param string $msg
     * @return array
     */
    public static function buildLongTextMessage($msg)
    {
        $msg = str_split($msg, 1999);
        $rr  = [];
        foreach ($msg as $val) {
            $rr[] = [
                "type" => "text",
                "text" => $val
            ];
        }
        return $rr;
    }

    public static function bg()
    {
        return new BackgroundProcess;
    }

    public static function profile($userid, $groupid = null)
    {
        if ($groupid) {
            return self::__exec("https://api.line.me/v2/bot/group/{$groupid}/member/{$userid}");
        } else {
            return self::__exec("https://api.line.me/v2/bot/profile/{$userid}");
        }
    }
    public static function push($data)
    {
        return self::__exec(
            "https://api.line.me/v2/bot/message/push",
            [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data)
            ]
        );
    }
    public static function getContent($msgid)
    {
        return self::__exec(
            "https://api.line.me/v2/bot/message/{$msgid}/content"
        );
    }
    private static function __exec($url, $opt = null)
    {
        $ch = curl_init($url);
        $__opt = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER        => [
                "Authorization: Bearer ".CHANNEL_ACCESS_TOKEN,
                "Content-Type: application/json"
            ],
            CURLOPT_TIMEOUT => 10
        ];
        if (is_array($opt)) {
            foreach ($opt as $key => $value) {
                $__opt[$key] = $value;
            }
        }
        curl_setopt_array($ch, $__opt);
        $out = curl_exec($ch);
        $no = curl_errno($ch) and $out = "Error ({$no}) : ".curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return [
            "content" => $out,
            "info" => $info
        ];
    }
}
