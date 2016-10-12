<div class="tabbable-line">
    <ul class="nav nav-tabs">


                @if ($empl_row->id == Auth::user()->id)
                    <li class="active">
                            <a href="#tab_leaves" data-toggle="tab" aria-expanded="true"> Leaves </a>
                    </li>
                    <li class="">
                            <a href="#tab_bonuses" data-toggle="tab" aria-expanded="false"> Bonuses </a>
                    </li>
                @endif

                <li class="{{ ($empl_row->id == Auth::user()->id) ? "" : "active" }}">
                        <a href="#tab_team" data-toggle="tab" aria-expanded="false"> Team </a>
                </li>
                <li class="">
                        <a href="#tab_achievements" data-toggle="tab" aria-expanded="false"> Achievements </a>
                </li>
                <li class="">
                        <a href="#tab_skills" data-toggle="tab" aria-expanded="false"> Skills </a>
                </li>

    </ul>
</div>