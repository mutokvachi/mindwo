<?php

namespace App\Libraries\Blocks\EmployeeCount;

use DB;
use Config;

/**
 * Report widget for total active employees
 */
class EmployeeCount_TEAM extends EmployeeCount
{
    private $counts;
    private $teams;

    /**
     * Get a list of all teams.
     * @return mixed
     */
    public function getGroups()
    {
        if ($this->teams) {
            return $this->teams;
        }

        $teams = \App\Models\Team::orderBy('title')->get();

        foreach ($teams as $team) {
            $this->teams[$team->id] = $team;
        }

        return $this->teams;
    }

    /**
     * Get an array with count of employees in each team.
     * @return mixed
     */
    public function getCounts($date)
    {
        if ($this->counts) {
            return $this->counts;
        }

        $counts = DB::table('dx_users')
                ->select(DB::raw('team_id, COUNT(*) AS count'))
                ->whereNotIn('id', Config::get('dx.empl_ignore_ids', [1]))
                ->whereNull('termination_date')
                ->groupBy('team_id')
                ->get();

        $total_count = $this->getTotalCount($counts);

        $unassignedCount = 0;

        foreach ($counts as $item) {
            if (!$item->team_id || !isset($this->getGroups()[$item->team_id])) {
                $unassignedCount = $item->count;
                continue;
            }

            $this->counts[$item->team_id] = [
                'count' => $item->count,
                'percent' => ($item->count / $total_count) * 100
            ];
        }

        if ($unassignedCount) {
            $this->counts['unassigned'] = [
                'count' => $unassignedCount,
                'percent' => ($unassignedCount / $total_count) * 100
            ];
        }

        return ['counts' => $this->counts, 'total_count' => $total_count];
    }
}