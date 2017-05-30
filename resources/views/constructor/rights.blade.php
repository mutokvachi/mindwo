@extends('constructor.common')

@section('constructor_content')
  <div class="col-md-12">
    <div class='table-scrollable'>
      <table class="table table-bordered table-hover">
        <tbody>
          <tr>
            <td width='30%'><a href="javascript:;"><i class="fa fa-key"></i> System administrators</a></td>
            <td><label class="badge badge-default">Can edit</label>&nbsp;<label class="badge badge-default">Can add
                new</label>&nbsp;<label class="badge badge-default">Can delete</label></td>
          </tr>
          <tr>
            <td width='30%'><a href="javascript:;"><i class="fa fa-key"></i> IT department</a></td>
            <td><label class="badge badge-default">Can edit</label>&nbsp;<label class="badge badge-default">Can add
                new</label>&nbsp;<label class="badge badge-default">Can delete</label></td>
          </tr>
          <tr>
            <td width='30%'><a href="javascript:;"><i class="fa fa-key"></i> Some example role name</a></td>
            <td><label class="badge badge-default">Can edit</label>&nbsp;<label class="badge badge-default">Can add
                new</label></td>
          </tr>
          <tr>
            <td width='30%'><a href="javascript:;"><i class="fa fa-key"></i> Example role for view only</a></td>
            <td>&nbsp;</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-12" style="text-align: center">
      <a href="javascript:;" class="btn btn-primary red dx-constructor-add-role">
        <i class='fa fa-plus'></i> {{ trans('constructor.add_role') }}
      </a>
    </div>
  </div>
@endsection
