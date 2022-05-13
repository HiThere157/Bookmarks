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
                    $info = explode(': ' , substr($log, 28));
                    $description = implode(': ', array_slice($info, 1));

                    //check for type
                    if($type != null && strtolower($type) != strtolower($info[0])){
                        continue;
                    }

                    //check for query
                    if($query != null && strpos(strtolower($description), strtolower($query)) === false){
                        continue;
                    }

                    //check for start and end time
                    if($start_time != null && $end_time != null){
                        $time = substr($log, 1, 19);
                        if($time < $start_time || $time > $end_time){
                            continue;
                        }
                    }

                    $description = explode("] ", $description);
                    $controller = $description[0] . "]";
                    $message = implode("] ", array_slice($description, 1));

                    //if no controller is found, undo split
                    if(!preg_match('/\[\w+@\w+\]/', $controller) == 1){
                        $message = $controller . " " . $message;
                        $controller = "N/A";
                    }

                    $logs[] = [
                        'time' => substr($log, 1, 19),
                        'type' => $info[0],
                        'controller' => $controller,
                        'description' => $message
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
