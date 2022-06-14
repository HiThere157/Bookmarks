<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class LogController extends Controller
{
    //GET: ingex page for the logs
    public function index(Request $request)
    {
        $type = $request->input('type');
        $query = $request->input('q');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');

        $n = $request->input('n'); 
        if(!$n) {
            $n = 20;
        }

        $logs = array();
        $files = glob(storage_path('logs/*.log'));
        foreach ($files as $file) {
            foreach(explode("\n", file_get_contents($file)) as $log){
                if(preg_match('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].+:.+/', $log) == 1){
                    $log_infos = explode(': ' , $log);
                    $log_type = explode('.', explode(' ', $log_infos[0])[2])[1];
                    $log_description = implode(': ', array_slice($log_infos, 1));
                    $log_time = substr($log, 1, 19);

                    //check for type
                    if($type != null && strtolower($type) != strtolower($log_type)){
                        continue;
                    }

                    //check for query
                    if($query != null && strpos(strtolower($log_description), strtolower($query)) === false){
                        continue;
                    }

                    //check for start and end time
                    if($start_time != null && $end_time != null){
                        if($log_time < $start_time || $log_time > $end_time){
                            continue;
                        }
                    }

                    $log_description = explode("] ", $log_description);
                    $log_controller = $log_description[0] . "]";
                    $log_message = implode("] ", array_slice($log_description, 1));

                    //if no controller is found, undo split
                    if(!preg_match('/\[\w+@\w+\]/', $log_controller) == 1){
                        $log_message = $log_controller . " " . $log_message;
                        $log_controller = "N/A";
                    }

                    $logs[] = [
                        'time' => $log_time,
                        'type' => $log_type,
                        'controller' => $log_controller,
                        'description' => $log_message
                    ];
                }
            }
        }

        $pageinator = $this->paginate(array_reverse($logs), $n)->withQueryString()->withPath('/logs');

        Log::info('[LogController@index] User ' . auth()->user()->username . ' accessed the log page.');
        return view('pages.logs', [
            'logs' => $pageinator,
            'type' => $type,
            'query' => $query,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'n' => $n
        ]);
    }

    private function paginate($items, $perPage, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page);
    }
}
