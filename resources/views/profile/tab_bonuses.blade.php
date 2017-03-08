<div class="tab-pane fade" id="tab_bonuses">
  @if (Auth::user()->id == 1)
    <a class="btn btn-primary pull-right btn-sm dx-employee-bonus-add-btn" style="margin-bottom: 20px; margin-top: 10px;"><i class="fa fa-plus"></i>
      Add bonus </a>
  @endif
  <div class="table-scrollable">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th> Title</th>
          <th> From</th>
          <th> To</th>
          <th> Description</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td> American Express</td>
          <td> Jun 12, 2016</td>
          <td> Jun 11, 2018</td>
          <td> Travel insurance included, daily limit 20000 EUR</td>
        </tr>
        <tr>
          <td> Car</td>
          <td> Jun 12, 2016</td>
          <td> Jun 11, 2018</td>
          <td> Lamborghini Diablo, 24x7 usage, no limits</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>