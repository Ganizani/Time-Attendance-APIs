<?php
/**
 * Created by PhpStorm.
 * User: carlos_pambo
 * Date: 3/17/18
 * Time: 8:19 AM
 */
namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportLog extends Model
{
    protected $table = 'report_logs';

    protected $fillable = [
        'report_name',
        'from_date',
        'to_date',
        'company_id',
        'site_id',
        'user_id',
    ];
}