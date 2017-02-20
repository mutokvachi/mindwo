<?php

namespace App\Libraries\Blocks\EmployeeCount;

use DB;
use Config;

/**
 * Report widget for total active employees
 */
class EmployeeCount_DEPARTMENT extends EmployeeCount
{
    private $counts;
    private $sources;
    private $departments;

    /**
     * Get count of employees in each source.
     * @return mixed
     */
    public function getCounts($date)
    {
        if ($this->counts) {
            return $this->counts;
        }
        
        if ($date == new \DateTime()) {
            $counts = DB::table('dx_users')
                    ->select(DB::raw('department_id, COUNT(*) AS count'))
                    ->whereNotIn('id', Config::get('dx.empl_ignore_ids', [1]))
                    ->whereNull('termination_date')
                    ->groupBy('department_id')
                    ->get();
        } else {
            $empl_ignore_ids = implode(',',  Config::get('dx.empl_ignore_ids', [1]));
            
            $list_id =  Config::get('dx.employee_list_id');   
            
            $field_id = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'department_id')->first()->id;
            
            $args = [
                'list_id' => $list_id, 
                'field_id' => $field_id,
                'fltr_date' =>$date->format('Y-m-d'),
                'empl_ignore_ids' => $empl_ignore_ids];
            
            $counts = DB::select('SELECT t.department_id, SUM(t.count) as count
                FROM(
                                (SELECT u.department_id, COUNT(u.id) AS count
                                FROM dx_users u
                                WHERE (u.termination_date IS NULL OR u.termination_date > :fltr_date)
                                        AND NOT FIND_IN_SET(u.id, :empl_ignore_ids)
                                        AND (u.join_date IS NULL OR u.join_date <= :fltr_date)
                                        AND u.id NOT IN 	(select e.item_id AS user_id
                                 from 
                                     (select e.item_id, max(e.event_time) as event_time
                                     from dx_db_history h
                                     left join dx_db_events e ON e.id = h.event_id
                                     left join dx_users u ON e.item_id = u.id
                                     where e.list_id = :list_id
                                         AND h.field_id = :field_id
                                         AND e.event_time <= :fltr_date
                                         AND NOT FIND_IN_SET(e.item_id, :empl_ignore_ids)
                                         AND (u.termination_date IS NULL OR u.termination_date > :fltr_date)
                                         AND (u.join_date IS NULL OR u.join_date <= :fltr_date)
                                     group by item_id) as e_new

                                 left join dx_db_events e ON e_new.item_id = e.item_id AND e_new.event_time = e.event_time
                                 left join  dx_db_history h  ON e.id = h.event_id
                                 right join dx_users u ON u.id = e.item_id
                                 where e.list_id = :list_id
                                     AND h.field_id = :field_id)
                                GROUP BY u.department_id
                                )
                        UNION
                                (select h.new_val_rel_id AS department_id, COUNT(e.item_id) AS count
                                 from 
                                     (select e.item_id, max(e.event_time) as event_time
                                     from dx_db_history h
                                     left join dx_db_events e ON e.id = h.event_id
                                     left join dx_users u ON e.item_id = u.id
                                     where e.list_id = :list_id
                                         AND h.field_id = :field_id
                                         AND e.event_time <= :fltr_date
                                         AND NOT FIND_IN_SET(e.item_id, :empl_ignore_ids)
                                         AND (u.termination_date IS NULL OR u.termination_date > :fltr_date)
                                         AND (u.join_date IS NULL OR u.join_date <= :fltr_date)
                                     group by item_id) as e_new

                                 left join dx_db_events e ON e_new.item_id = e.item_id AND e_new.event_time = e.event_time
                                 left join  dx_db_history h  ON e.id = h.event_id
                                 right join dx_users u ON u.id = e.item_id
                                 where e.list_id = :list_id
                                     AND h.field_id = :field_id
                                GROUP BY h.new_val_rel_id)) AS t
                GROUP BY t.department_id', $args);
              
            //\Log::info('data: ' . json_encode($counts));
            // DB::getQueryLog()
            /*var_dump($counts);
            var_dump(DB::getQueryLog());
            DB::disableQueryLog();*/
            //\Log::info('query: ' . );   
        }

        $total_count = $this->getTotalCount($counts);

        $unassignedCount = 0;

        foreach ($counts as $item) {
            if (!$item->department_id || !isset($this->getDepartments()[$item->department_id])) {
                $unassignedCount = $item->count;
                continue;
            }

            $source = $this->getDepartments()[$item->department_id]->source_id;

            if (!isset($this->counts[$source])) {
                $this->counts[$source]['count'] = $item->count;
            } else {
                $this->counts[$source]['count'] += $item->count;
            }

            $this->counts[$source]['percent'] = ($this->counts[$source]['count'] / $total_count) * 100;
        }

        if ($unassignedCount) {
            $this->counts['unassigned'] = [
                'count' => $unassignedCount,
                'percent' => ($unassignedCount / $total_count) * 100
            ];
        }

        return ['counts' => $this->counts, 'total_count' => $total_count];
    }

    /**
     * Get a list of sources.
     * @return mixed
     */
    public function getGroups()
    {
        if ($this->sources) {
            return $this->sources;
        }

        $sources = \App\Models\Source::orderBy('title')->get();

        foreach ($sources as $source) {
            $this->sources[$source->id] = $source;
        }

        return $this->sources;
    }

    /**
     * Get a list of departments.
     * @return mixed
     */
    public function getDepartments()
    {
        if ($this->departments) {
            return $this->departments;
        }

        $departments = \App\Models\Department::orderBy('title')->get();

        foreach ($departments as $department) {
            $this->departments[$department->id] = $department;
        }

        return $this->departments;
    }
}