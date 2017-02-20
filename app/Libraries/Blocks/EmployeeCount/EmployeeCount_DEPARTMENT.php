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
        
        if ($date == new \DateTime() || $date != new \DateTime()) {
            $counts = DB::table('dx_users')
                    ->select(DB::raw('department_id, COUNT(*) AS count'))
                    ->whereNotIn('id', Config::get('dx.empl_ignore_ids', [1]))
                    ->whereNull('termination_date')
                    ->groupBy('department_id')
                    ->get();
        } else {
            /*$counts = DB::table(DB::Raw('(select e.item_id, max(e.event_time) as event_time
                        from dx_db_history h
                        left join dx_db_events e ON e.id = h.event_id
                        where e.list_id = ?
                        AND h.field_id = ?
                        AND e.event_time <= ?
                        group by item_id) AS e_new', [259, 1642, $date]))
                    ->select(DB::raw('h.new_val_rel_id as department_id, COUNT(e.item_id) as count'))
                    ->leftJoin('dx_db_events AS e', function($join) {
                        $join->on('e_new.item_id', '=', 'e.item_id');
                        $join->on('e_new.event_time', '=', 'e.event_time');
                    })
                    ->leftJoin('dx_db_history AS h', 'e.id', '=', 'h.event_id')
                    ->whereNotIn('id', Config::get('dx.empl_ignore_ids', [1]))
                    ->where('e.list_id', 259)
                    ->where('h.field_id', 1642)
                    ->groupBy('h.new_val_rel_id');*/

         //   \Log::info($counts->toSql());
            $counts = $counts->get();
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