<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Carbon\CarbonImmutable;

class Gclog extends Model
{

    protected $guarded = [];

    /**
     * Parse GC Logs from input string.
     *
     * @param string $log_string
     * @return int|false Number of record processed. False on fail
     **/

    public static function parseLog(string $log_string)
    {
        $processedRecords = 0;
        $array_of_log = explode("\n", $log_string);

        foreach ($array_of_log as $eachLine) {
            $matches = [];

            //ParNew
            if (preg_match("/(.+?)\+.*\[ParNew:\s(.+?)K->(.+?)K\((.+?)K.+?\]\s(.+?)K->(.+?)K\((.+?)K/", $eachLine, $matches)) {
                $datetime = $matches[1];
                $newgen_before = $matches[2];
                $newgen_current = $matches[3];
                $newgen_maximum = $matches[4];
                $heap_before = $matches[5];
                $heap_current = $matches[6];
                $heap_maximum = $matches[7];

                Gclog::firstOrCreate([
                    'logtype' => "completed minor gc",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                    'newgen_before' => $newgen_before,
                    'newgen_current' => $newgen_current,
                    'newgen_maximum' => $newgen_maximum,
                    'oldgen_before' => $heap_before - $newgen_before,
                    'oldgen_current' => $heap_current - $newgen_current,
                    'oldgen_maximum' => $heap_maximum - $newgen_maximum,
                    'heap_before' => $heap_before,
                    'heap_current' => $heap_current,
                    'heap_maximum' => $heap_maximum,
                ]);

                $processedRecords++;



                continue;
            }

            //CMS Initial Mark
            if (preg_match("/\[1 CMS-initial-mark:/", $eachLine, $matches)) {
                preg_match("/(.+?)\+.+?mark:\s(.+?)K\((.+?)K\)\]\s(.+?)K\((.+?)K/", $eachLine, $matches);
                $datetime = $matches[1];
                $oldgen_current = $matches[2];
                $oldgen_maximum = $matches[3];
                $heap_current = $matches[4];
                $heap_maximum = $matches[5];
                $newgen_current = $heap_current - $oldgen_current;
                $newgen_maximum = $heap_maximum - $oldgen_maximum;

                Gclog::firstOrCreate([
                    'logtype' => "CMS-initial-mark",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                    'newgen_current' => $newgen_current,
                    'newgen_maximum' => $newgen_maximum,
                    'oldgen_current' => $heap_current - $newgen_current,
                    'oldgen_maximum' => $heap_maximum - $newgen_maximum,
                    'heap_current' => $heap_current,
                    'heap_maximum' => $heap_maximum,
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-mark-start
            if (preg_match("/\[1 CMS-initial-mark:/", $eachLine, $matches)) {
                preg_match("/(.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-initial-mark",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-mark
            if (preg_match("/CMS-concurrent-mark:/", $eachLine, $matches)) {
                preg_match("/(.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-concurrent-mark",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-preclean-start
            if (preg_match("/CMS-concurrent-preclean-start/", $eachLine, $matches)) {
                preg_match("/(.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-concurrent-preclean-start",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-preclean
            if (preg_match("/CMS-concurrent-preclean/", $eachLine, $matches)) {
                preg_match("/(.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-concurrent-preclean",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-abortable-preclean-start
            if (preg_match("/CMS-concurrent-abortable-preclean-start/", $eachLine, $matches)) {
                preg_match("/(.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-concurrent-abortable-preclean-start",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-abortable-preclean
            if (preg_match("/CMS-concurrent-abortable-preclean:/", $eachLine, $matches)) {
                preg_match("/(\d\d\d\d.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-concurrent-abortable-preclean",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-remark
            if (preg_match("/(.+?)\+.+?\[GC\[YG occupancy: (.+?) K \((.+?) K.+?remark:(.+?)K\((.+?)K\)\] (.+?)K\((.+?)K/", $eachLine, $matches)) {
                $datetime = $matches[1];
                $newgen_current = $matches[2];
                $newgen_maximum = $matches[3];
                $oldgen_current = $matches[4];
                $oldgen_maximum = $matches[5];
                $heap_current = $matches[6];
                $heap_maximum = $matches[7];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-remark",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                    'newgen_current' => $newgen_current,
                    'newgen_maximum' => $newgen_maximum,
                    'oldgen_current' => $oldgen_current,
                    'oldgen_maximum' => $oldgen_maximum,
                    'heap_current' => $heap_current,
                    'heap_maximum' => $heap_maximum,
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-sweep-start
            if (preg_match("/CMS-concurrent-sweep-start/", $eachLine, $matches)) {
                preg_match("/(.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-concurrent-sweep-start",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-sweep
            if (preg_match("/CMS-concurrent-sweep/", $eachLine, $matches)) {
                preg_match("/(.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-concurrent-sweep",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-reset-start
            if (preg_match("/CMS-concurrent-reset-start/", $eachLine, $matches)) {
                preg_match("/(.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-concurrent-reset-start",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }

            //CMS-concurrent-reset
            if (preg_match("/CMS-concurrent-reset/", $eachLine, $matches)) {
                preg_match("/(.+?)\+/", $eachLine, $matches);
                $datetime = $matches[1];

                Gclog::firstOrCreate([
                    'logtype' => "CMS-concurrent-reset",
                    'server_name' => "J2EE-1",
                    'datetime' => Carbon::createFromFormat("Y-m-d\TH:i:s.v", $datetime)->format("Y-m-d H:i:s.v"),
                ]);

                $processedRecords++;

                continue;
            }
        }

        return $processedRecords;
    }

    public static function getLastOneMonth()
    {
        if (self::latest('datetime')->first() !== null) {
            $lastDateTime = self::latest('datetime')->first()->datetime;
        } else {
            return null;
        }
        
        $lastEntryDatetime = new CarbonImmutable($lastDateTime);
        $oneMonthBeforeLastEntryDatetime = $lastEntryDatetime->subMonth();

        return self::whereBetween('datetime', [$oneMonthBeforeLastEntryDatetime, $lastEntryDatetime])->oldest('datetime')->get();
    }

    public static function getLastOneWeek()
    {
        $lastDateTime = self::latest('datetime')->first()->datetime;

        if(!$lastDateTime) {
            return null;
        }

        $lastEntryDatetime = new CarbonImmutable();
        $oneWeekBeforeLastEntryDatetime = $lastEntryDatetime->subMonth();

        return self::whereBetween('datetime', [$lastEntryDatetime, $oneWeekBeforeLastEntryDatetime])->get();
    }

    public static function getLastOneDay()
    {
        $lastDateTime = self::latest('datetime')->first()->datetime;

        if(!$lastDateTime) {
            return null;
        }

        $lastEntryDatetime = new CarbonImmutable();
        $oneDayBeforeLastEntryDatetime = $lastEntryDatetime->subMonth();

        return self::whereBetween('datetime', [$lastEntryDatetime, $oneDayBeforeLastEntryDatetime])->get();
    }

}
