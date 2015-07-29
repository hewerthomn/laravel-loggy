<?php namespace Hewerthomn\Loggy;

use Illuminate\Database\Eloquent\Model;
use Auth, DB, Config, Request;

/**
* Loggy
*/
class Loggy extends Model
{
  protected $table = 'loggy';

  protected static $SUCCESS   = 'success';
  protected static $WARNING   = 'warning';
  protected static $INFO      = 'info';
  protected static $ERROR     = 'error';
  protected static $EXCEPTION = 'exception';

  public $timestamps = false;

  public function user()
  {
    return $this->belongsTo(Config::get('loggy.userClass', 'App\User'));
  }

  public function cssClass()
  {
    switch ($this->type)
    {
      case self::$SUCCESS:
        return Config::get('loggy.classes.success', 'success');

      case self::$INFO:
        return Config::get('loggy.classes.info', 'info');

      case self::$WARNING:
        return Config::get('loggy.classes.warning', 'warning');

      case self::$ERROR:
        return Config::get('loggy.classes.error', 'error');

      case self::$EXCEPTION:
        return Config::get('loggy.classes.exception', 'exception');
    }
  }

  public function icon()
  {
    switch ($this->type)
    {
      case self::$SUCCESS: return Config::get('loggy.icons.success', '<i class="fa fa-ok"></i>');
      case self::$INFO: return Config::get('loggy.icons.info', '<i class="fa fa-info-sign"></i>');
      case self::$WARNING: return Config::get('loggy.icons.warning', '<i class="fa fa-warning-sign"></i>');
      case self::$ERROR: return Config::get('loggy.icons.error', '<i class="fa fa-exclamation-sign"></i>');
      case self::$EXCEPTION: return Config::get('loggy.icons.exception', '<i class="fa fa-fire"></i>');
    }
  }

  public function types()
  {
    $types['all']            = 'All types';
    $types[self::$SUCCESS]   = 'Success';
    $types[self::$INFO]      = 'Information';
    $types[self::$WARNING]   = 'Warning';
    $types[self::$ERROR]     = 'Error';
    $types[self::$EXCEPTION] = 'Exception';

    return $types;
  }

  public function apps()
  {
    return $this->groupBy('app_id')->lists('app_id', 'app_id');
  }

  public function filter($request)
  {
    $app_id = $request->has('app_id') ? $request->input('app_id') : Config::get('loggy.app_id');

    $logs = new self;

    if($request->has('start_at'))
    {
      $startAt = \DateTime::createFromFormat('d/m/Y', Input::get('start_at'));
      $startAt = $startAt->format('Y-m-d');
    }
    else
    {
      $startAt = date('Y-m-d');
    }

    if($request->has('end_at'))
    {
      $endAt = \DateTime::createFromFormat('d/m/Y', Input::get('end_at'));
      $endAt = $endAt->format('Y-m-d');
    }
    else
    {
      $endAt = date('Y-m-d');
    }

    $logs = $this->where('app_id', '=', $app_id)
                 ->whereRaw(DB::raw("DATE(created_at) >= '{$startAt}'"))
                 ->whereRaw(DB::raw("DATE(created_at) <= '{$endAt}'"));

    if($request->has('type') && $request->input('type') !== 'all')
    {
      $logs = $logs->where('type', '=', $request->input('type'));
    }

    if($request->has('q'))
    {
      $query = '%'.strtolower($request->input('q')).'%';
      $logs = $logs->where(DB::raw('LOWER(title)'), 'LIKE', $query)
                   ->orWhere(DB::raw('LOWER(message)'), 'LIKE', $query);
    }

    return $logs->orderBy('created_at', 'DESC')->get();
  }

  public function today($app_id = null)
  {
    $today = date('Y-m-d');
    $app_id = $app_id === null ? Config::get('loggy.app_id') : $app_id;

    return $this->where('app_id', '=', $app_id)
                ->whereRaw(DB::raw("DATE(created_at) = '{$today}'"))
                ->orderBy('created_at', 'DESC');
  }

  public function between($startAt = null, $endAt = null)
  {
    if($startAt)
    {
      $startAt = \DateTime::createFromFormat('d/m/Y', Input::get('start_at'));
      $startAt = $startAt->format('Y-m-d');
    }
    else
    {
      $startAt = date('Y-m-d');
    }

    if($endAt)
    {
      $endAt = \DateTime::createFromFormat('d/m/Y', Input::get('end_at'));
      $endAt = $endAt->format('Y-m-d');
    }
    else
    {
      $endAt = date('Y-m-d');
    }

    $logs = $this->loggy->where('app_id', '=', $app_id)
                        ->whereRaw(DB::raw("DATE(created_at) >= '{$startAt}'"))
                        ->whereRaw(DB::raw("DATE(created_at) <= '{$endAt}'"));

    if($type !== null)
    {
      $logs = $logs->where('type', '=', $type);
    }

    return $logs->orderBy('created_at', 'DESC')->get();
  }

  private function log($type, $title, $message)
  {
    $log = new self;
    $log->type  = $type;
    $log->title = $title;
    $log->message  = $message;
    $log->app_id = Config::get('loggy.app_id');
    $log->user_id = Auth::user() ? Auth::user()->id : null;
    $log->created_at = new \DateTime();
    $log->request = json_encode(Request::all());

    @$log->save();
  }

  public static function success($title, $message)
  {
    $log = new self;
    $log->log(self::$SUCCESS, $title, $message);
  }

  public static function info($title, $message)
  {
    $log = new self;
    $log->log(self::$INFO, $title, $message);
  }

  public static function warning($title, $message)
  {
    $log = new self;
    $log->log(self::$WARNING, $title, $message);
  }

  public static function error($title, $message)
  {
    $log = new self;
    $log->log(self::$ERROR, $title, $message);
  }

  public static function exception($title, $ex)
  {
    $log = new self;

    $message  = "{$ex->getFile()} @ {$ex->getLine()}\n\n";
    $message .= "{$ex->getMessage()} \n\n{$ex->getTraceAsString()}";

    $log->log(self::$EXCEPTION, $title, $message);
  }
}