<?php

namespace App\Http\Resources;

use App\Models\{Place, MenuTag, Behavior, Menu};
use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

class StatisticsResource extends Resource
{
    private $param;

    public function __construct($resource, $param = false) {
        // Ensure we call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        $this->param = $param; // $param param passed
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        switch ($this->param) {
            case 'totalMonthIncome':
                // 构造数组
                for ($i=0; $i < 12; $i++) { 
                    $data[$i]['month'] = $i+1;
                    $data[$i]['price'] = 0;
                    $data[$i]['number'] = 0;

                    // 树状图
                    $month[$i] = $i+1;
                    $price[$i] = 0;
                }

                $orders = $this->orders()->whereYear('created_at', $request->year)
                                            ->whereIn('status', [1, 2])
                                            ->selectRaw('month(created_at) as month, sum(final_price) as price, sum(sitter) as number')
                                            ->groupBy('month')
                                            ->get()
                                            ->toArray();

                foreach ($data as $key => $value) {
                    foreach ($orders as $k => $v) {
                        if ($value['month'] == $v['month']) {
                            $data[$key]['price'] = $v['price'];
                            $data[$key]['number'] = $v['number'];
                        }
                    }
                }

                foreach ($price as $key => $value) {
                    foreach ($orders as $k => $v) {
                        if ($key == ($v['month'] -1)) {
                            $price[$key] = $v['price']/1000;
                        }
                    }
                }

                return [
                    'data' => $data,
                    'month' => $month,
                    'price' => $price,
                ];
                break;

            case 'eachMonthIncome':
                // 获取指定时间月份天数
                $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
                $orders = $this->orders()->whereMonth('created_at', $request->month)
                                            ->whereIn('status', [1, 2])
                                            ->selectRaw('day(created_at) as day, sum(final_price) as price, sum(sitter) as number')
                                            ->groupBy('day')
                                            ->get()
                                            ->toArray();

                // 构造数组
                for ($i=0; $i < $days; $i++) { 
                    $data[$i]['day'] = $i+1;
                    $data[$i]['price'] = 0;
                    $data[$i]['number'] = 0;
                }

                foreach ($data as $key => $value) {
                    foreach ($orders as $k => $v) {
                        if ($value['day'] == $v['day']) {
                            $data[$key]['price'] = $v['price'];
                            $data[$key]['number'] = $v['number'];
                        }
                    }
                }

                return [
                    'data' => $data
                ];
                break;

            case 'eachDayIncome':
                // 组合日期
                $date = $request->year.'-'.$request->month.'-'.$request->day;
                $orders = $this->orders()->whereDate('created_at', $date)->whereIn('status', [1, 2])->get()->map(function ($item){
                    $item->place_name = Place::where('id', $item->place_id)->value('name');
                    return $item->only('place_name', 'sitter', 'final_price');
                })->values();

                return [
                    'data' => $orders
                ];
                break;

            case 'totalWeekIncome':
                $get_week = $this->get_week(date('Y', $request->date));

                foreach ($get_week as $key => $value) {
                    if (strtotime($value['startday']) < $request->date && strtotime($value['endday']) >= $request->date) {
                        $period = $value['period'];
                    }
                    $betweenDay[$key] = [$value['startday'], $value['endday']];
                    $orders[$key] = $this->orders()->whereBetween('created_at', $betweenDay[$key])
                                                        ->whereIn('status', [1, 2])
                                                        ->selectRaw('sum(final_price) as price, sum(sitter) as number')
                                                        ->get()
                                                        ->toArray();

                    $get_week[$key]['price'] = $orders[$key][0]['price']?:0;
                    $get_week[$key]['number'] = $orders[$key][0]['number']?:0;
                }

                if (($period-10) >= 0) {
                    for ($i=$period; $i>($period-10); $i--) { 
                        $week[] = $i;
                    }
                } else {
                    for ($i=$period; $i>0; $i--) { 
                        $week[] = $i;
                    }
                }
                sort($week);

                foreach ($week as $key => $value) {
                    $betweenDay[$key] = [$get_week[$value-1]['startday'], $get_week[$value-1]['endday']];
                    $price[$key] = $this->orders()->whereBetween('created_at', $betweenDay[$key])
                                                    ->whereIn('status', [1, 2])
                                                    ->selectRaw('sum(final_price) as price')
                                                    ->get()
                                                    ->toArray()[0]['price']/1000?:0;
                }

                return [
                    'data' => $get_week,
                    'week' => $week,
                    'price' => $price,
                ];
                break;

            case 'eachWeekIncome':
                $weekly = $this->getDateFromRange($request->startday, $request->endday);
                foreach ($weekly as $key => $value) {
                    $week[$key] = date('m-d', strtotime($value));
                }

                foreach ($weekly as $key => $value) {
                    $data[$key] = $this->orders()->whereDate('created_at', $value)
                                                    ->whereIn('status', [1, 2])
                                                    ->selectRaw('sum(final_price) as price, sum(sitter) as number')
                                                    ->get()
                                                    ->toArray()[0];
                }

                $price = array();
                foreach ($data as $key => $value) {
                    $data[$key]['date'] = $weekly[$key];
                    $data[$key]['price'] = $value['price']?:0;
                    $data[$key]['number'] = $value['number']?:0;
                    $price[$key] = $value['price']/1000?:0;
                }

                return [
                    'data' => $data,
                    'week' => $week,
                    'price' => $price,
                ];
                break;

            case 'guestMoment':
                $weekly = $this->getDateFromRange($request->startday, $request->endday);

                $time = array(0, 6, 12, 18, 24);
                for ($i=0; $i < count($time); $i++) { 
                    for ($j=0; $j < count($weekly); $j++) { 
                        switch ($i) {
                            case 0:
                                $betweenDay[$j] = [$weekly[$j]. ' 00:00:00', $weekly[$j]. ' 00:00:00'];
                                break;
                            case 1:
                                $betweenDay[$j] = [$weekly[$j]. ' 00:00:00', $weekly[$j]. ' 0'. ($time[$i]-1).':59:59'];
                                break;
                            case 2:
                                $betweenDay[$j] = [$weekly[$j]. ' 0'.$time[$i-1]. ':00:00', $weekly[$j]. ' '. ($time[$i]-1).':59:59'];
                                break;
                            default:
                                $betweenDay[$j] = [$weekly[$j]. ' '.$time[$i-1]. ':00:00', $weekly[$j]. ' '. ($time[$i]-1).':59:59'];
                                break;
                        }
                        $number[$i][$j] = (int)$this->orders()->whereBetween('created_at', $betweenDay[$j])
                                                                ->whereIn('status', [1, 2])
                                                                ->selectRaw('sum(sitter) as number')
                                                                ->get()
                                                                ->toArray()[0]['number']?:0;
                    }
                }

                foreach ($number as $key => $value) {
                    $number[$key] = array_sum($value);
                }

                for ($i=0; $i < 24; $i++) { 
                    for ($j=0; $j < count($weekly); $j++) { 
                        if ($i == 0) {
                            $betweenDay[$j] = [$weekly[$j]. ' 00:00:00', $weekly[$j]. ' 00:00:00'];
                        } else if($i < 10){
                            $betweenDay[$j] = [$weekly[$j]. ' 0'. ($i-1). ':00:00', $weekly[$j]. ' 0'. ($i-1) .':59:59'];
                        } else {
                            $betweenDay[$j] = [$weekly[$j]. ' '. ($i-1). ':00:00', $weekly[$j]. ' '. ($i-1) .':59:59'];
                        }

                        $totalNumber[$i][$j] = $this->orders()->whereBetween('created_at', $betweenDay[$j])
                                                                    ->whereIn('status', [1, 2])
                                                                    ->selectRaw('sum(sitter) as number')
                                                                    ->get()
                                                                    ->toArray()[0]['number']?:0;
                    }
                }

                foreach ($totalNumber as $key => $value) {
                    $data[$key]['number'] = array_sum($value);
                    if ($key < 10) {
                        $data[$key]['time'] = '0'.$key.':00';
                    } else {
                        $data[$key]['time'] = $key.':00';
                    }
                }

                return [
                    'data' => $data,
                    'time' => $time,
                    'number' => $number,
                ];
                break;

            case 'menuRank':
                $menus = $this->menus()->where('status', 1)->select('id', 'name')->get()->map(function ($item){
                    $item->price = 0;
                    $item->number = 0;
                    return $item;
                });

                $collection = $this->orders()->whereBetween('created_at', [$request->startday. ' 00:00:00', $request->endday. ' 23:59:59'])->whereIn('status', [1, 2])->get()->map(function ($item){
                    return $item->orders()->where('pid', 0)->selectRaw('id, menu_id, sum(price) as price, sum(number) as number')->whereIn('status', [1, 2, 3, 4])->groupBy('menu_id')->get()->toArray();
                })->filter()->values();
                
                if ($collection->isNotEmpty()) {
                    $newdata = [];
                    foreach($collection as $key => $value){
                        foreach ($value as $k => $v) {
                            if(!isset($newdata[$v['menu_id']])){
                                $newdata[$v['menu_id']] = $v;
                            }else{
                                $newdata[$v['menu_id']]['price'] += $v['price'];
                                $newdata[$v['menu_id']]['number'] += $v['number'];
                            }
                        }
                    }

                    foreach ($newdata as $key => $value) {
                        if ($key) {
                            $value['name'] = Menu::where('id', $value['menu_id'])->value('name');
                            $data[] = $value;
                        }
                    }
                    
                    foreach ($menus as $key => $value) {
                        foreach ($data as $k => $v) {
                            if ($value['id'] == $v['menu_id']) {
                                $menus[$key]['price'] = $v['price'];
                                $menus[$key]['number'] = $v['number'];
                            }
                        }
                    }

                    switch ($request->type) {
                        case 'price':
                            $menus = $menus->sortByDesc('price')->values();
                            break;
                        case 'number':
                            $menus = $menus->sortByDesc('number')->values();
                            break;
                    }
                }

                return [
                    'data' => $menus,
                ];                

                break;

            case 'placeRank':
                // 门店下所有座位
                $places = $this->places()->where('floor', '<>', 0)->select('id', 'name')->get()->map(function ($item){
                    $item->price = 0;
                    $item->sitter = 0;
                    return $item;
                });
                
                $collection = $this->orders()->whereBetween('created_at', [$request->startday. ' 00:00:00', $request->endday. ' 23:59:59'])
                                                ->whereIn('status', [1, 2])
                                                ->selectRaw('place_id, sum(final_price) as price, sum(sitter) as sitter')
                                                ->groupBy('place_id')
                                                ->get()->map(function ($item){
                                                    $item->place_name = Place::where('id', $item->place_id)->value('name');
                                                    return $item;
                                                });

                if ($collection->isNotEmpty()) {
                    $places->map(function ($item) use ($collection){
                        $collection->map(function ($value) use ($item){
                            if ($item->id == $value->place_id) {
                                $item->price = $value->price;
                                $item->sitter = $value->sitter;
                            }
                        });
                    });

                    switch ($request->type) {
                        case 'price':
                            $places = $places->sortByDesc('price')->values();
                            break;
                        case 'sitter':
                            $places = $places->sortByDesc('sitter')->values();
                            break;
                    }
                }

                return [
                    'data' => $places,
                ];             

                break;

            case 'staffService':
                // 门店下所有员工
                $users = $this->users()->select('id', 'name')->get()->map(function ($item){
                    $item->book = 0;
                    $item->order = 0;
                    $item->serving = 0;
                    $item->clean = 0;
                    $item->settle = 0;
                    return $item;
                });

                $collection = $this->users->map(function ($item) use ($request){
                    return $item->behaviors()->whereBetween('created_at', [$request->startday. ' 00:00:00', $request->endday. ' 23:59:59'])
                                                ->whereIn('category', ['book', 'order', 'serving', 'clean', 'settle'])
                                                ->where('status', 1)
                                                ->selectRaw('user_id, count(*) as count, category')
                                                ->groupBy('category')
                                                ->get()
                                                ->toArray();
                })->filter()->values();

                if ($collection->isNotEmpty()) {
                    $users->map(function ($item) use ($collection){
                        foreach ($collection[0] as $key => $value) {
                            if ($item->id == $value['user_id']) {
                                $item[$value['category']] += $value['count'];
                            }
                        }
                    });

                    switch ($request->type) {
                        case 'book':
                            $users = $users->sortByDesc('book')->values();
                            break;
                        case 'order':
                            $users = $users->sortByDesc('order')->values();
                            break;
                        case 'serving':
                            $users = $users->sortByDesc('serving')->values();
                            break;
                        case 'clean':
                            $users = $users->sortByDesc('clean')->values();
                            break;
                        case 'settle':
                            $users = $users->sortByDesc('settle')->values();
                            break;
                    }
                }
                
                return [
                    'data' => $users,
                ];

                break;

            case 'menuServed':
                // 门店下所有菜品
                $menus = $this->menus()->where('status', 1)->select('id', 'name')->get()->map(function ($item){
                    $item->number = 0;
                    $item->time = 0;
                    $item->fast_time = '0:0';
                    $item->slow_time = '0:0';
                    $item->averages = '0:0';
                    return $item;
                });

                $collection = $this->orders()->whereBetween('created_at', [$request->startday. ' 00:00:00', $request->endday. ' 23:59:59'])->whereIn('status', [1, 2])->get()->map(function ($item){
                    return $item->orders()->where('category', 'm')->whereIn('status', [1, 2, 3, 4])->selectRaw('id, menu_id, menus_id, number')->get()->map(function ($item){
                        $behavior = $item->behavior()->where('category', 'cooking')->where('status', 1)->first();
                        $item->time = carbon::parse($behavior['updated_at'])->diffInSeconds($behavior['created_at'],true);
                        if ($item->menu_id) {
                            $item->menu_name = Menu::where('id', $item->menu_id)->value('name');
                        } else {
                            $item->menu_name = Menu::where('id', $item->menus_id)->value('name');
                        }
                        return $item;
                    });
                })->collapse()->toArray();

                if ($collection) {
                    $newdata = [];
                    foreach ($collection as $key => $value) {
                        if ($value['menu_id']) {
                            if(!isset($newdata[$value['menu_id']])){
                                $newdata[$value['menu_id']] = $value;
                            }else{
                                $newdata[$value['menu_id']]['time'] += $value['time'];
                                $newdata[$value['menu_id']]['number'] += $value['number'];
                            }
                            $newdata[$value['menu_id']]['data'][] = $value['time'];
                        } else {
                            if(!isset($newdata[$value['menus_id']])){
                                $newdata[$value['menus_id']] = $value;
                            }else{
                                $newdata[$value['menus_id']]['time'] += $value['time'];
                                $newdata[$value['menus_id']]['number'] += $value['number'];
                            }
                            $newdata[$value['menus_id']]['data'][] = $value['time'];
                        }
                    }

                    $data = [];
                    foreach ($newdata as $key => $value) {
                        array_multisort($newdata[$key]['data'], SORT_DESC);
                        $count = count($value['data']);
                        if ($count == 1) {
                            $newdata[$key]['fast_time'] = floor(($value['data'][0])/3600).':'.floor(((($value['data'][0])%3600)/60)).':'.(($value['data'][0])%60);
                            $newdata[$key]['slow_time'] = floor(($value['data'][0])/3600).':'.floor(((($value['data'][0])%3600)/60)).':'.(($value['data'][0])%60);
                        } else {
                            $newdata[$key]['fast_time'] = floor(($value['data'][0])/3600).':'.floor(((($value['data'][0])%3600)/60)).':'.(($value['data'][0])%60);
                            $newdata[$key]['slow_time'] = floor(($value['data'][$count-1])/3600).':'.floor(((($value['data'][$count-1])%3600)/60)).':'.(($value['data'][$count-1])%60);
                        }
                    }
                    foreach ($newdata as $key => $value) {
                        // 平均数
                        $value['averages'] = floor(($value['time']/$value['number'])/3600).':'.floor(((($value['time']/$value['number'])%3600)/60)).':'.(($value['time']/$value['number'])%60);
                        $data[] = $value;
                    }

                    $menus->map(function ($item) use ($data){
                        foreach ($data as $key => $value) {
                            if ($value['menu_id']) {
                                if ($item->id == $value['menu_id']) {
                                    $item->number = $value['number'];
                                    $item->time = $value['time'];
                                    $item->fast_time = $value['fast_time'];
                                    $item->slow_time = $value['slow_time'];
                                    $item->averages = $value['averages'];
                                }
                            } else {
                                if ($item->id == $value['menus_id']) {
                                    $item->number = $value['number'];
                                    $item->time = $value['time'];
                                    $item->fast_time = $value['fast_time'];
                                    $item->slow_time = $value['slow_time'];
                                    $item->averages = $value['averages'];
                                }
                            }
                        }
                    });

                    switch ($request->type) {
                        case 'fast_time':
                            $menus = $menus->sortByDesc('fast_time')->values();
                            break;
                        case 'slow_time':
                            $menus = $menus->sortByDesc('slow_time')->values();
                            break;
                        case 'averages':
                            $menus = $menus->sortByDesc('averages')->values();
                            break;
                    }
                }

                return [
                    'data' => $menus,
                ];
                break;
        }
    }
}
