<?php
/**
 * 处理PHP输入
 */

namespace app\common\behavior;

class ParseInputBehavior
{
    public function run()
    {
        // echo MODULE_NAME . CONTROLLER_NAME . ACTION_NAME . PHP_EOL;
        // exit();
        // 兼容Content Type为application/json的数据接收
        if (isset($_SERVER['CONTENT_TYPE']) && preg_match("/\w+\/\w+([\-\+]\w+)*/", $_SERVER['CONTENT_TYPE'], $result)) {
            $content_type = $result[0];
            switch ($content_type) {
                case 'multipart/form-data':
                case 'application/x-www-form-urlencoded':
                    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                        try {
                            $this->parsePut();
                        } catch (\Exception $e) {

                        }
                        global $_PUT;
                        $_POST = $_PUT;
                    }
                    break;
                case 'application/json':
                    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                        global $_PUT;
                        $php_input = file_get_contents('php://input');
                        $_PUT = (array) json_decode($php_input, 1);
                        $_POST = $_PUT;
                    } else {
                        $php_input = file_get_contents('php://input');
                        $_POST = (array) json_decode($php_input, 1);
                    }

                    break;
                case 'application/xml':
                case 'text/xml':
                    $php_input = file_get_contents('php://input');
                    // 听说并没有成熟的xml解析工具？
                    break;
            }
        }

    }

    private function parsePut()
    {
        global $_PUT;

        /* PUT data comes in on the stdin stream */
        $putdata = fopen("php://input", "r");

        /* Open a file for writing */
        // $fp = fopen("myputfile.ext", "w");

        $raw_data = '';

        /* Read the data 1 KB at a time
        and write to the file */
        while ($chunk = fread($putdata, 1024)) {
            $raw_data .= $chunk;
        }

        /* Close the streams */
        fclose($putdata);

        // Fetch content and determine boundary
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        if (empty($boundary)) {
            parse_str($raw_data, $data);
            $GLOBALS['_PUT'] = $data;
            return $data;
        }

        // Fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = array();

        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") {
                break;
            }

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                $tmp_name = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;

                //Parse File
                if (isset($matches[4])) {

                    //if labeled the same as previous, skip
                    if (isset($_FILES[$matches[2]])) {
                        continue;
                    }

                    //get filename
                    $filename = $matches[4];

                    //get tmp name
                    $filename_parts = pathinfo($filename);
                    $tmp_name = tempnam(ini_get('upload_tmp_dir'), 'php');
                    // $tmp_name       = tempnam(ini_get('upload_tmp_dir'), $filename_parts['filename']);

                    //populate $_FILES with information, size may be off in multibyte situation
                    if (strstr($matches[2], '[]') === false) {
                        $file_key = $matches[2];
                        $_FILES[$file_key] = array(
                            'error' => 0,
                            'name' => $filename,
                            'tmp_name' => $tmp_name,
                            'size' => strlen($body),
                            'type' => $value,
                        );
                    } else {
                        $file_key = str_replace('[]', '', $matches[2]);
                        $_FILES[$file_key]['error'][] = 0;
                        $_FILES[$file_key]['name'][] = $filename;
                        $_FILES[$file_key]['tmp_name'][] = $tmp_name;
                        $_FILES[$file_key]['size'][] = strlen($body);
                        $_FILES[$file_key]['type'][] = $value;
                    }

                    //place in temporary directory
                    file_put_contents($tmp_name, $body);
                } //Parse Field
                else {
                    if (strstr($name, '[]') === false) {
                        $data[$name] = substr($body, 0, strlen($body) - 2);
                    } else {
                        $sr_name = str_replace('[]', '', $name);
                        $data[$sr_name][] = substr($body, 0, strlen($body) - 2);
                    }
                }
            }

        }
        $GLOBALS['_PUT'] = $data;
        return $data;
    }
}
