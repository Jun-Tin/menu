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
            // case 'guestNumber':
            //     // 当天
            //     $today_total = $this->orders()->whereDate('created_at', Carbon::today())->sum('sitter');
            //     // 当天日期
            //     $today_cycle = Carbon::now()->format('m.d');

            //     // 本月
            //     $month_total = $this->orders()->whereMonth('created_at', Carbon::now()->month)->sum('sitter');
            //     // 本月日期
            //     $month_cycle = Carbon::now()->format('m');

            //     if ($request->exists('year')) {
            //         // 按月排序
            //         $month_total_all = $this->orders()->whereYear('created_at', $request->year)
            //                                     ->selectRaw('month(created_at) as month, sum(sitter) as value')
            //                                     ->groupBy('month')
            //                                     ->get()
            //                                     ->toArray();
            //         // 按日排序
            //         $date_total_all = $this->orders()->whereYear('created_at', $request->year)
            //                                     ->selectRaw('month(created_at) as month, day(created_at) as day, sum(sitter) as value')
            //                                     ->groupBy('month', 'day')
            //                                     ->get()
            //                                     ->toArray();
            //         // 当前年 - 查询年（非当年默认显示12个月）
            //         if (Carbon::now()->format('Y') - $request->year != 0) {
            //             $month_cycle = 12;
            //         }

            //         // 本年
            //         $year_total = $this->orders()->whereYear('created_at', $request->year)->sum('sitter');
            //         // 本年日期
            //         $year_cycle = $request->year;
            //     } else {
            //         // 截止当前月份
            //         $month_until = [Carbon::now()->format('Y').'-01-01',Carbon::now()->format('Y').'-'.$month_cycle.'-31'];
            //         // 按月排序
            //         $month_total_all = $this->orders()->whereBetween('created_at', $month_until)
            //                                     ->selectRaw('month(created_at) as month, sum(sitter) as value')
            //                                     ->groupBy('month')
            //                                     ->get()
            //                                     ->toArray();
            //         // 按日排序
            //         $date_total_all = $this->orders()->whereBetween('created_at', $month_until)
            //                                     ->selectRaw('month(created_at) as month, day(created_at) as day, sum(sitter) as value')
            //                                     ->groupBy('month', 'day')
            //                                     ->get()
            //                                     ->toArray();
            //         // 本年
            //         $year_total = $this->orders()->whereYear('created_at', Carbon::now()->year)->sum('sitter');
            //         // 本年日期
            //         $year_cycle = Carbon::now()->format('Y');
            //     }

            //     $year_total_all = 0;
            //     // 按照年度查询
            //     if ($request->exists('start_time') && $request->exists('end_time')) {
            //         // 年度
            //         $year_cycle_all = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
            //         // 年度人数
            //         $year_total_all = $this->orders()->whereBetween('created_at', $year_cycle_all)->sum('sitter');
            //     }

            //     // 构造数组
            //     for ($i=0; $i < $month_cycle; $i++) {
            //         $data[$i]['date'] = $i+1;
            //         $data[$i]['value'] = 0;
            //     }

            //     foreach ($data as $key => $value) {
            //         foreach ($month_total_all as $k => $v) {
            //             if ($value['date'] == $v['month']) {
            //                 $data[$key]['value'] = $v['value'];
            //             }
            //         }
            //         foreach ($date_total_all as $k => $v) {
            //             if ($value['date'] == $v['month']) {
            //                 $data[$key]['data'][] = $v;
            //             }
            //         }
            //     }
            //     return [
            //         'today_cycle' => $today_cycle, 
            //         'today_total' => $today_total,
            //         'year_total_all' => $year_total_all, 
            //         'month_cycle' => $month_cycle,
            //         'month_total' => $month_total, 
            //         'year_cycle' => $year_cycle,
            //         'year_total' => $year_total,
            //         'data' => $data, 
            //     ];
            //     break;

            // case 'guestMoment':
            //     if ($request->exists('start_time') && $request->exists('end_time')) {
            //         $time = [$request->start_day.' '.$request->start_time, $request->end_day.' '.$request->end_time];
            //     } else {
            //         $time = [$request->start_day.' 00:00:00', $request->end_day.' 23:59:59'];
            //     }
            //     // 区间总人数
            //     $total = $this->orders()->whereBetween('created_at', $time)->sum('sitter');
                
            //     $data = $this->orders()->whereBetween('created_at', $time)->selectRaw('place_id, sum(sitter) as value')->groupBy('place_id')->get()->map(function ($item){
            //         $item->name = Place::where('id', $item->place_id)->value('name');
            //         return $item->only('name', 'value');
            //     });
            //     return [
            //         'total' => $total,
            //         'data' => $data
            //     ];
            //     break;

            // case 'menuRank':
            //     $data = array();
            //     // 门店下所有标签
            //     $category = $this->tags()->where('pid', 0)->where('category', 'class')->select('id', 'name')->get();
            //     // 门店下所有菜品
            //     $menus = $this->menus()->select('id', 'name', 'category')->get()->map(function ($item){
            //         $item->tags = MenuTag::where('menu_id', $item->id)->orWhere('pid', 0)->distinct()->pluck('target_id')->toArray();
            //         return $item->only('id', 'name', 'category', 'tags');
            //     })->toArray();
            //     // 查询时间
            //     if ($request->exists('start_time') && $request->exists('end_time')) {
            //         $time = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
            //         $collection = $this->order_details()->whereBetween('orders.created_at', $time)->where('pid', 0)->selectRaw('menu_id, sum(order_details.number) as value, order_details.category')->groupBy('menu_id')->orderBy('value', 'desc')->get()->map(function ($item){
            //             return $item->only('menu_id', 'value');
            //         })->toArray();

            //         foreach ($menus as $key => $value) {
            //             $menus[$key]['value'] = 0;
            //             foreach ($collection as $k => $v) {
            //                 if ($value['id'] == $v['menu_id']) {
            //                     $menus[$key]['value'] = $v['value'];
            //                 }
            //             }
            //         }

            //         switch ($request->category) {
            //             case '0':
            //                 $data = $menus;
            //                 break;
            //             case 'p':
            //                 foreach ($menus as $key => $value) {
            //                     if ($value['category'] == 'p') {
            //                         $data[] = $value;
            //                     }
            //                 }
            //                 break;
            //             default:
            //                 foreach ($menus as $key => $value) {
            //                     if (in_array($request->category, $value['tags'])) {
            //                         $data[] = $value;
            //                     }
            //                 }
            //                 break;
            //         }
            //     }
            //     // 重新排序
            //     array_multisort(array_column($data, 'value'), SORT_DESC, $data);

            //     return [
            //         'category' => $category, 
            //         'data' => $data
            //     ];
            //     break;

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

            // case 'menuServed':
            //     // 声明变量 
            //     $averages = '0:0:0';

            //     if ($request->exists('start_time') && $request->exists('end_time')) {
            //         $time = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];
            //         $collection = $this->order_details()->whereIn('order_details.status', [3,4])->get()->map(function ($item) use ($time){
            //             if ($item->menu_id) {
            //                 $item->name = Menu::where('id', $item->menu_id)->value('name');
            //             } else {
            //                 $item->name = Menu::where('id', $item->menus_id)->value('name');
            //             }
            //             $item->behaviors = Behavior::where('target_id', $item->id)->where('category', 'serving')->whereBetween('created_at', $time)->first();
            //             return $item->only('behaviors', 'name');
            //         });

            //         $collection = $collection->map(function ($item){
            //             if ($item['behaviors']) {
            //                 $item['value'] = floor((carbon::parse($item['behaviors']['updated_at'])->diffInMinutes($item['behaviors']['created_at'],true)/60)).':'.(carbon::parse($item['behaviors']['updated_at'])->diffInMinutes($item['behaviors']['created_at'],true)%60).':'.(carbon::parse($item['behaviors']['updated_at'])->diffInSeconds($item['behaviors']['created_at'],true)%60);
            //                 $item['time'] = carbon::parse($item['behaviors']['updated_at'])->diffInSeconds($item['behaviors']['created_at'],true);

            //                 return [
            //                     'name' => $item['name'],
            //                     'value' => $item['value'],
            //                     'time' => $item['time'],
            //                 ];
            //             }
            //         })->filter()->values();

            //         if (!$collection->isEmpty()) {
            //             // 总条数
            //             $count = $collection->count();
            //             // 总时间数
            //             $time_total = 0;
            //             foreach ($collection as $key => $value) {
            //                 $time_total += $value['time'];
            //             }
            //             // 格式化平均时间数
            //             $averages = floor(($time_total/$count)/3600).':'.floor(((($time_total/$count)%3600)/60)).':'.(($time_total/$count)%60);
            //             // 格式化总时间数
            //             $total = floor($time_total/3600).':'.floor((($time_total%3600)/60)).':'.($time_total%60);
            //         } else {
            //             $data = array();
            //         }
            //     }

            //     return [
            //         'data' => $collection,
            //         'averages' => $averages,
            //         // 'count' => $count,
            //         // 'total' => $total,
            //     ];
            //     break;

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
                $orders = $this->orders()->whereDate('created_at', $date)->get()->map(function ($item){
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
                    if ($value['startday'] < date('Y-m-d', $request->date) && $value['endday'] >= date('Y-m-d', $request->date)) {
                        $period = $value['period'];
                    }
                    $betweenDay[$key] = [$value['startday'], $value['endday']];
                    $orders[$key] = $this->orders()->whereBetween('created_at', $betweenDay[$key])
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
                    // DATE_FORMAT(created_at,"%Y-%m")
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
                $collection = $this->orders()->whereBetween('created_at', [$request->startday. ' 00:00:00', $request->endday. ' 23:59:59'])->get()->map(function ($item){
                    return $item->orders()->where('pid', 0)->selectRaw('id, menu_id, sum(price) as price, sum(number) as number')->get()->toArray()[0];
                })->filter()->values();

                if ($collection->isNotEmpty()) {
                    $newdata = [];
                    foreach($collection as $k=>$v){
                        if(!isset($newdata[$v['menu_id']])){
                            $newdata[$v['menu_id']] = $v;
                        }else{
                            $newdata[$v['menu_id']]['price'] += $v['price'];
                            $newdata[$v['menu_id']]['number'] += $v['number'];
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
                    $users->map(function ($item, $key) use ($collection){
                        foreach ($collection[0] as $k => $value) {
                            if ($item->id == $value['user_id']) {
                                if ($value['category'] == $key) {
                                    $item[$value['category']] += $value['count'];
                                }
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
                
                break;
        }
    }
}
