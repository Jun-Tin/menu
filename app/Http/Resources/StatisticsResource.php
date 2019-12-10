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
            case 'guestNumber':
                // 当天
                $today_total = $this->orders()->whereDate('created_at', Carbon::today())->sum('sitter');
                // 当天日期
                $today_cycle = Carbon::now()->format('m.d');

                // 本月
                $month_total = $this->orders()->whereMonth('created_at', Carbon::now()->month)->sum('sitter');
                // 本月日期
                $month_cycle = Carbon::now()->format('m');

                if ($request->exists('year')) {
                    // 按月排序
                    $month_total_all = $this->orders()->whereYear('created_at', $request->year)
                                                ->selectRaw('month(created_at) as month, sum(sitter) as value')
                                                ->groupBy('month')
                                                ->get()
                                                ->toArray();
                    // 按日排序
                    $date_total_all = $this->orders()->whereYear('created_at', $request->year)
                                                ->selectRaw('month(created_at) as month, day(created_at) as day, sum(sitter) as value')
                                                ->groupBy('month', 'day')
                                                ->get()
                                                ->toArray();
                    // 当前年 - 查询年（非当年默认显示12个月）
                    if (Carbon::now()->format('Y') - $request->year != 0) {
                        $month_cycle = 12;
                    }

                    // 本年
                    $year_total = $this->orders()->whereYear('created_at', $request->year)->sum('sitter');
                    // 本年日期
                    $year_cycle = $request->year;
                } else {
                    // 截止当前月份
                    $month_until = [Carbon::now()->format('Y').'-01-01',Carbon::now()->format('Y').'-'.$month_cycle.'-31'];
                    // 按月排序
                    $month_total_all = $this->orders()->whereBetween('created_at', $month_until)
                                                ->selectRaw('month(created_at) as month, sum(sitter) as value')
                                                ->groupBy('month')
                                                ->get()
                                                ->toArray();
                    // 按日排序
                    $date_total_all = $this->orders()->whereBetween('created_at', $month_until)
                                                ->selectRaw('month(created_at) as month, day(created_at) as day, sum(sitter) as value')
                                                ->groupBy('month', 'day')
                                                ->get()
                                                ->toArray();
                    // 本年
                    $year_total = $this->orders()->whereYear('created_at', Carbon::now()->year)->sum('sitter');
                    // 本年日期
                    $year_cycle = Carbon::now()->format('Y');
                }

                $year_total_all = 0;
                // 按照年度查询
                if ($request->exists('start_time') && $request->exists('end_time')) {
                    // 年度
                    $year_cycle_all = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
                    // 年度人数
                    $year_total_all = $this->orders()->whereBetween('created_at', $year_cycle_all)->sum('sitter');
                }

                // 构造数组
                for ($i=0; $i < $month_cycle; $i++) {
                    $data[$i]['date'] = $i+1;
                    $data[$i]['value'] = 0;
                }

                foreach ($data as $key => $value) {
                    foreach ($month_total_all as $k => $v) {
                        if ($value['date'] == $v['month']) {
                            $data[$key]['value'] = $v['value'];
                        }
                    }
                    foreach ($date_total_all as $k => $v) {
                        if ($value['date'] == $v['month']) {
                            $data[$key]['data'][] = $v;
                        }
                    }
                }
                return [
                    'today_cycle' => $today_cycle, 
                    'today_total' => $today_total,
                    'year_total_all' => $year_total_all, 
                    'month_cycle' => $month_cycle,
                    'month_total' => $month_total, 
                    'year_cycle' => $year_cycle,
                    'year_total' => $year_total,
                    'data' => $data, 
                ];
                break;

            case 'guestMoment':
                if ($request->exists('start_time') && $request->exists('end_time')) {
                    $time = [$request->start_day.' '.$request->start_time, $request->end_day.' '.$request->end_time];
                } else {
                    $time = [$request->start_day.' 00:00:00', $request->end_day.' 23:59:59'];
                }
                // 区间总人数
                $total = $this->orders()->whereBetween('created_at', $time)->sum('sitter');
                
                $data = $this->orders()->whereBetween('created_at', $time)->selectRaw('place_id, sum(sitter) as value')->groupBy('place_id')->get()->map(function ($item){
                    $item->name = Place::where('id', $item->place_id)->value('name');
                    return $item->only('name', 'value');
                });
                return [
                    'total' => $total,
                    'data' => $data
                ];
                break;

            case 'menuRank':
                $data = array();
                // 门店下所有标签
                $category = $this->tags()->where('pid', 0)->where('category', 'class')->select('id', 'name')->get();
                // 门店下所有菜品
                $menus = $this->menus()->select('id', 'name', 'category')->get()->map(function ($item){
                    $item->tags = MenuTag::where('menu_id', $item->id)->orWhere('pid', 0)->distinct()->pluck('target_id')->toArray();
                    return $item->only('id', 'name', 'category', 'tags');
                })->toArray();
                // 查询时间
                if ($request->exists('start_time') && $request->exists('end_time')) {
                    $time = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
                    $collection = $this->order_details()->whereBetween('orders.created_at', $time)->where('pid', 0)->selectRaw('menu_id, sum(order_details.number) as value, order_details.category')->groupBy('menu_id')->orderBy('value', 'desc')->get()->map(function ($item){
                        return $item->only('menu_id', 'value');
                    })->toArray();

                    foreach ($menus as $key => $value) {
                        $menus[$key]['value'] = 0;
                        foreach ($collection as $k => $v) {
                            if ($value['id'] == $v['menu_id']) {
                                $menus[$key]['value'] = $v['value'];
                            }
                        }
                    }

                    switch ($request->category) {
                        case '0':
                            $data = $menus;
                            break;
                        case 'p':
                            foreach ($menus as $key => $value) {
                                if ($value['category'] == 'p') {
                                    $data[] = $value;
                                }
                            }
                            break;
                        default:
                            foreach ($menus as $key => $value) {
                                if (in_array($request->category, $value['tags'])) {
                                    $data[] = $value;
                                }
                            }
                            break;
                    }
                }
                // 重新排序
                array_multisort(array_column($data, 'value'), SORT_DESC, $data);

                return [
                    'category' => $category, 
                    'data' => $data
                ];
                break;

            case 'moneyRank':
                $data = array();
                // 门店下所有标签
                $category = $this->tags()->where('pid',0)->where('category', 'class')->select('id', 'name')->get();
                // 门店下所有菜品
                $menus = $this->menus()->select('id', 'name', 'type', 'original_price', 'special_price', 'category')->get()->map(function ($item){
                    switch ($item->type) {
                        case 'o':
                            $item->price = $item->original_price;
                            break;
                        default:
                            $item->price = $item->special_price;
                            break;
                    }
                    $item->tags = MenuTag::where('menu_id', $item->id)->orWhere('pid', 0)->distinct()->pluck('target_id')->toArray();
                    return $item->only('id', 'name', 'price', 'category', 'tags');
                })->toArray();
                
                // 查询时间
                if ($request->exists('start_time') && $request->exists('end_time')) {
                    $time = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
                    $collection = $this->order_details()->whereBetween('orders.created_at', $time)->where('pid', 0)->selectRaw('menu_id, sum(order_details.number) as value, order_details.category')->groupBy('menu_id')->orderBy('value', 'desc')->get()->map(function ($item){
                        return $item->only('menu_id', 'value');
                    })->toArray();

                    foreach ($menus as $key => $value) {
                        $menus[$key]['value'] = 0;
                        foreach ($collection as $k => $v) {
                            if ($value['id'] == $v['menu_id']) {
                                $menus[$key]['value'] = $v['value'];
                            }
                        }
                        $menus[$key]['total_price'] = $menus[$key]['price']*$menus[$key]['value'];
                    }

                    switch ($request->category) {
                        case '0':
                            $data = $menus;
                            break;
                        case 'p':
                            foreach ($menus as $key => $value) {
                                if ($value['category'] == 'p') {
                                    $data[] = $value;
                                }
                            }
                            break;
                        default:
                            foreach ($menus as $key => $value) {
                                if (in_array($request->category, $value['tags'])) {
                                    $data[] = $value;
                                }
                            }
                            break;
                    }
                }
                // 重新排序
                array_multisort(array_column($data, 'total_price'), SORT_DESC, $data);

                return [
                    'category' => $category,
                    'data' => $data
                ];
                break;

            case 'placeNumber':
                // 当天
                $today_total = $this->orders()->whereDate('created_at', Carbon::today())->count();
                // 当天日期
                $today_cycle = Carbon::now()->format('m.d');

                // 本月
                $month_total = $this->orders()->whereMonth('created_at', Carbon::now()->month)->count();
                // 本月日期
                $month_cycle = Carbon::now()->format('m');


                if ($request->exists('year')) {
                    // 按月排序
                    $month_total_all = $this->orders()->whereYear('created_at', $request->year)
                                                ->selectRaw('month(created_at) as month, count(*) as value')
                                                ->groupBy('month')
                                                ->get()
                                                ->toArray();
                    // 按日排序
                    $date_total_all = $this->orders()->whereYear('created_at', $request->year)
                                                ->selectRaw('month(created_at) as month, day(created_at) as day, count(*) as value')
                                                ->groupBy('month', 'day')
                                                ->get()
                                                ->toArray();
                    // 当前年 - 查询年（非当年默认显示12个月）
                    if (Carbon::now()->format('Y') - $request->year != 0) {
                        $month_cycle = 12;
                    }

                    // 本年
                    $year_total = $this->orders()->whereYear('created_at', $request->year)->count();
                    // 本年日期
                    $year_cycle = $request->year;
                } else {
                    // 截止当前月份
                    $month_until = [Carbon::now()->format('Y').'-01-01',Carbon::now()->format('Y').'-'.$month_cycle.'-31'];
                    // 按月排序
                    $month_total_all = $this->orders()->whereBetween('created_at', $month_until)
                                                ->selectRaw('month(created_at) as month, count(*) as value')
                                                ->groupBy('month')
                                                ->get()
                                                ->toArray();
                    // 按日排序
                    $date_total_all = $this->orders()->whereBetween('created_at', $month_until)
                                                ->selectRaw('month(created_at) as month, day(created_at) as day, count(*) as value')
                                                ->groupBy('month', 'day')
                                                ->get()
                                                ->toArray();
                    // 本年
                    $year_total = $this->orders()->whereYear('created_at', Carbon::now()->year)->count();
                    // 本年日期
                    $year_cycle = Carbon::now()->format('Y');
                }
                
                $year_total_all = 0;
                // 按照年度查询
                if ($request->exists('start_time') && $request->exists('end_time')) {
                    // 年度
                    $year_cycle_all = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
                    // 年度桌数（订单数）
                    $year_total_all = $this->orders()->whereBetween('created_at', $year_cycle_all)->count();
                }

                // 构造数组
                for ($i=0; $i < $month_cycle; $i++) {
                    $data[$i]['date'] = $i+1;
                    $data[$i]['value'] = 0;
                }

                foreach ($data as $key => $value) {
                    foreach ($month_total_all as $k => $v) {
                        if ($value['date'] == $v['month']) {
                            $data[$key]['value'] = $v['value'];
                        }
                    }
                    foreach ($date_total_all as $k => $v) {
                        if ($value['date'] == $v['month']) {
                            $data[$key]['data'][] = $v;
                        }
                    }
                }

                return [
                    'today_cycle' => $today_cycle, 
                    'today_total' => $today_total,
                    'year_total_all' => $year_total_all, 
                    'month_cycle' => $month_cycle,
                    'month_total' => $month_total, 
                    'year_cycle' => $year_cycle,
                    'year_total' => $year_total,
                    'data' => $data, 
                ];
                break;

            case 'placeHolder':
                // 声明变量 
                $collection = array();
                $count = 0;
                $averages = '0:0:0';
                $total = '0:0:0';

                if ($request->exists('start_time') && $request->exists('end_time')) {
                    $time = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
                    
                    $collection = $this->orders()->whereBetween('created_at', $time)->selectRaw('place_id, created_at, updated_at')->get();
                    if (!$collection->isEmpty()) {
                        $collection->map(function ($item){
                            $item->name = Place::where('id', $item->place_id)->value('name');
                            $item->place_time = floor((carbon::parse($item->updated_at)->diffInMinutes($item->created_at,true)/60)).':'.(carbon::parse($item->updated_at)->diffInMinutes($item->created_at,true)%60).':'.(carbon::parse($item->updated_at)->diffInSeconds($item->created_at,true)%60);
                            $item->time = carbon::parse($item->updated_at)->diffInSeconds($item->created_at,true);
                            return $item;
                        })->toArray();
                        // 总条数
                        $count = $collection->count();
                        // 总时间数
                        $time_total = 0;
                        foreach ($collection as $key => $value) {
                            $time_total += $value['time'];
                        }
                        // 格式化平均时间数
                        $averages = floor(($time_total/$count)/3600).':'.floor(((($time_total/$count)%3600)/60)).':'.(($time_total/$count)%60);
                        // 格式化总时间数
                        $total = floor($time_total/3600).':'.floor((($time_total%3600)/60)).':'.($time_total%60);
                    }
                }

                return [
                    'data' => $collection, 
                    'count' => $count, 
                    'averages' => $averages, 
                    'total' => $total
                ];
                break;

            case 'menuServed':
                // 声明变量 
                $averages = '0:0:0';

                if ($request->exists('start_time') && $request->exists('end_time')) {
                    $time = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
                    $collection = $this->order_details()->whereIn('order_details.status', [3,4])->get()->map(function ($item) use ($time){
                        if ($item->menu_id) {
                            $item->name = Menu::where('id', $item->menu_id)->value('name');
                        } else {
                            $item->name = Menu::where('id', $item->menus_id)->value('name');
                        }
                        $item->behaviors = Behavior::where('target_id', $item->id)->where('category', 'serving')->whereBetween('created_at', $time)->first();
                        return $item->only('behaviors', 'name');
                    });

                    $collection = $collection->map(function ($item){
                        if ($item['behaviors']) {
                            $item['value'] = floor((carbon::parse($item['behaviors']['updated_at'])->diffInMinutes($item['behaviors']['created_at'],true)/60)).':'.(carbon::parse($item['behaviors']['updated_at'])->diffInMinutes($item['behaviors']['created_at'],true)%60).':'.(carbon::parse($item['behaviors']['updated_at'])->diffInSeconds($item['behaviors']['created_at'],true)%60);
                            $item['time'] = carbon::parse($item['behaviors']['updated_at'])->diffInSeconds($item['behaviors']['created_at'],true);

                            return [
                                'name' => $item['name'],
                                'value' => $item['value'],
                                'time' => $item['time'],
                            ];
                        }
                    })->filter()->values();

                    if (!$collection->isEmpty()) {
                        // 总条数
                        $count = $collection->count();
                        // 总时间数
                        $time_total = 0;
                        foreach ($collection as $key => $value) {
                            $time_total += $value['time'];
                        }
                        // 格式化平均时间数
                        $averages = floor(($time_total/$count)/3600).':'.floor(((($time_total/$count)%3600)/60)).':'.(($time_total/$count)%60);
                        // 格式化总时间数
                        $total = floor($time_total/3600).':'.floor((($time_total%3600)/60)).':'.($time_total%60);
                    } else {
                        $data = array();
                    }
                }

                return [
                    'data' => $collection,
                    'averages' => $averages,
                    // 'count' => $count,
                    // 'total' => $total,
                ];
                break;

            case 'income':
                // 当天
                $today_total = $this->orders()->whereDate('created_at', Carbon::today())->selectRaw('sum(final_price) as value')->first()->value?:0;
                // 当天日期
                $today_cycle = Carbon::now()->format('m.d');

                // 本月
                $month_total = $this->orders()->whereMonth('created_at', Carbon::now()->month)->selectRaw('sum(final_price) as value')->first()->value?:0;
                // 本月日期
                $month_cycle = Carbon::now()->format('m');


                if ($request->exists('year')) {
                    // 按月排序
                    $month_total_all = $this->orders()->whereYear('created_at', $request->year)
                                                ->selectRaw('month(created_at) as month, sum(final_price) as value')
                                                ->groupBy('month')
                                                ->get()
                                                ->toArray();
                    // 按日排序
                    $date_total_all = $this->orders()->whereYear('created_at', $request->year)
                                                ->selectRaw('month(created_at) as month, day(created_at) as day, sum(final_price) as value')
                                                ->groupBy('month', 'day')
                                                ->get()
                                                ->toArray();
                    // 当前年 - 查询年（非当年默认显示12个月）
                    if (Carbon::now()->format('Y') - $request->year != 0) {
                        $month_cycle = 12;
                    }

                    // 本年
                    $year_total = $this->orders()->whereYear('created_at', $request->year)->selectRaw('sum(final_price) as value')->first()->value?:0;
                    // 本年日期
                    $year_cycle = $request->year;
                } else {
                    // 截止当前月份
                    $month_until = [Carbon::now()->format('Y').'-01-01',Carbon::now()->format('Y').'-'.$month_cycle.'-31'];
                    // 按月排序
                    $month_total_all = $this->orders()->whereBetween('created_at', $month_until)
                                                ->selectRaw('month(created_at) as month, sum(final_price) as value')
                                                ->groupBy('month')
                                                ->get()
                                                ->toArray();
                    // 按日排序
                    $date_total_all = $this->orders()->whereBetween('created_at', $month_until)
                                                ->selectRaw('month(created_at) as month, day(created_at) as day, sum(final_price) as value')
                                                ->groupBy('month', 'day')
                                                ->get()
                                                ->toArray();
                    // 本年
                    $year_total = $this->orders()->whereYear('created_at', Carbon::now()->year)->selectRaw('sum(final_price) as value')->first()->value?:0;
                    // 本年日期
                    $year_cycle = Carbon::now()->format('Y');
                }
                
                $year_total_all = 0;
                // 按照年度查询
                if ($request->exists('start_time') && $request->exists('end_time')) {
                    // 年度
                    $year_cycle_all = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
                    // 年度桌数（订单数）
                    $year_total_all = $this->orders()->whereBetween('created_at', $year_cycle_all)->selectRaw('sum(final_price) as value')->first()->value?:0;
                }

                // 构造数组
                for ($i=0; $i < $month_cycle; $i++) {
                    $data[$i]['date'] = $i+1;
                    $data[$i]['value'] = 0;
                }

                foreach ($data as $key => $value) {
                    foreach ($month_total_all as $k => $v) {
                        if ($value['date'] == $v['month']) {
                            $data[$key]['value'] = $v['value'];
                        }
                    }
                    foreach ($date_total_all as $k => $v) {
                        if ($value['date'] == $v['month']) {
                            $data[$key]['data'][] = $v;
                        }
                    }
                }

                return [
                    'today_cycle' => $today_cycle, 
                    'today_total' => $today_total,
                    'year_total_all' => $year_total_all, 
                    'month_cycle' => $month_cycle,
                    'month_total' => $month_total, 
                    'year_cycle' => $year_cycle,
                    'year_total' => $year_total,
                    'data' => $data, 
                ];
                break;
        }
    }
}
