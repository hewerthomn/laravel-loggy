<?php namespace Hewerthomn\Loggy;

use Illuminate\Database\Eloquent\Model;
use Auth, Config;

/**
* Loggy
*/
class Loggy extends Model
{
  protected $table = 'loggy';

  protected static $SUCCESS   = 's';
  protected static $WARNING   = 'w';
  protected static $INFO      = 'i';
  protected static $ERROR     = 'e';
  protected static $EXCEPTION = 'ex';

  public $timestamps = false;

  private function loggy($type, $title, $message)
  {
    $log = new self;
    $log->type  = $type;
    $log->title = $title;
    $log->message  = $message;
    $log->app_id = Config::get('loggy.app_id');
    $log->user_id = Auth::user() ? Auth::user()->id : null;
    $log->created_at = new \DateTime();

    @$log->save();
  }

  public static function log($data)
  {
    $log = new self;
    $log->loggy($data['type'], $data['title'], $data['message']);
  }

  public static function success($title, $message)
  {
    $log = new self;
    $log->loggy(self::$SUCCESS, $title, $message);
  }

  public static function info($title, $message)
  {
    $log = new self;
    $log->loggy(self::$INFO, $title, $message);
  }

  public static function warning($title, $message)
  {
    $log = new self;
    $log->loggy(self::$WARNING, $title, $message);
  }

  public static function error($title, $message)
  {
    $log = new self;
    $log->loggy(self::$ERROR, $title, $message);
  }

  public static function exception($title, $ex)
  {
    $log = new self;

    $message  = "{$ex->getFile()} @ {$ex->getLine()}\n\n";
    $message .= "{$ex->getMessage()} \n\n{$ex->getTraceAsString()}";

    $log->loggy(self::$EXCEPTION, $title, $message);
  }
}