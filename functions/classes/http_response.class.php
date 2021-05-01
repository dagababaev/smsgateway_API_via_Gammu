<?php

/**
 * Created by Agababaev Dmitriy © 2020
 * d.agababaev @ duncat.net
 * License MIT
 */

class http_response
{
    public static function return($code, $data = null) {
        if (@$data['success']) $message['success'] = $data['success'];
        switch ($code) {
            case 200: header('HTTP/1.1 200 OK'); break;
            case 201: header('HTTP/1.1 201 Created'); break;
            case 202: header('HTTP/1.1 202 Accepted'); break;
            case 204: header('HTTP/1.1 204 No Content'); break;

            case 400: header('HTTP/1.1 400 Bad Request');
                $message['error_code'] = 400;
                $message['error_message'] = 'Bad Request';
                break;
            case 401: header('HTTP/1.1 401 Unauthorized');
                $message['error_code'] = 401;
                $message['error_message'] = 'Unauthorized';
                break;
            case 403: header('HTTP/1.1 403 Forbidden');
                $message['error_code'] = 403;
                $message['error_message'] = 'Forbidden';
                break;
            case 404: header('HTTP/1.1 404 Not Found');
                $message['error_code'] = 404;
                $message['error_message'] = 'Not Found';
                break;
            case 406: header('HTTP/1.1 406 Not Acceptable');
                $message['error_code'] = 406;
                $message['error_message'] = 'Not Acceptable';
                break;
        }
        if(isset($data['description'])) $message['description'] = $data['description'];
        die(json_encode($message));
    }
}
