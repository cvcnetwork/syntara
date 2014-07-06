@extends(Config::get('syntara::views.master'))

@section('content')

<script src="{{ asset('packages/mrjuliuss/syntara/assets/js/dashboard/user.js') }}"></script>
<div class="container" id="main-container">
    <div class="row">
        <div class="col-lg-8">
            <section class="module">
                <div class="module-head">
                    <b>{{ $user->getId() }} - {{ $user->username }}</b>
                </div>
                <div class="module-body">
                    <form class="form-horizontal" id="edit-user-form" method="PUT">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label">{{ trans('syntara::users.username') }}</label>
                                <input class="col-lg-12 form-control" type="text" id="username" name="username" value="{{ $user->username}}">
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('syntara::all.email') }}</label>
                                <input class="col-lg-12 form-control" type="text" id="email" name="email" value="{{ $user->email }}">
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('syntara::all.password') }}</label>
                                <input class="col-lg-12 form-control" type="password" placeholder="{{ trans('syntara::all.password') }}" id="pass" name="pass" >
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('syntara::users.last-name') }}</label>
                                <input class="col-lg-12 form-control" type="text" id="last_name" name="last_name" value="{{ $user->last_name  }}">
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('syntara::users.first-name') }}</label>
                                <input class="col-lg-12 form-control" type="text" id="first_name" name="first_name" value="{{ $user->first_name }}">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Timezone</label>
                                <select class="col-lg-12 form-control" type="text" id="timezone" name="timezone" value="<?php echo getTzOptions($user->timezone) ?>"></select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label">{{ trans('syntara::users.groups') }}</label>
                            </div>
                            <div class="form-group">

                            @foreach($groups as $group)
                            <label class="checkbox-inline">
                                @if($currentUser->hasAccess('user-group-management'))
                                <input type="checkbox" id="groups[{{ $group->getId() }}]" name="groups[]" value="{{ $group->getId() }}" {{ ($user->inGroup($group)) ? 'checked="checked"' : ''}}>
                                @endif
                                {{ $group->getName() }}
                            </label>
                            @endforeach
                            </div>
                        </div>
                        <div class="col-lg-5">
                            @if($currentUser->hasAccess('permissions-management'))
                                @include(Config::get('syntara::views.permissions-select'), array('permissions'=> $permissions))
                            @endif
                        </div>
                        @if($user->getId() !== $currentUser->getId())
                        <div class="col-lg-2">
                            <label>{{ trans('syntara::users.banned') }}</label>
                            <div class="switch-toggle well">
                                <input id="no" name="banned" type="radio" value="no" {{ ($throttle->isBanned() === false) ? 'checked' : '' }}>
                                <label for="no" onclick="">{{ trans('syntara::all.no') }}</label>

                                <input id="yes" name="banned" type="radio" value="yes" {{ ($throttle->isBanned() === true) ? 'checked' : '' }}>
                                <label for="yes" onclick="">{{ trans('syntara::all.yes') }}</label>

                                <a class="btn btn-primary"></a>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <div class="form-group">
                                <button id="update-user" class="btn btn-primary">{{ trans('syntara::all.update') }}</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </section>
        </div>
        <div class="col-lg-4">
            <section class="module">
            <div class="module-head">
                <b>{{ trans('syntara::users.information') }}</b>
            </div>
            <div class="module-body ajax-content">
                @include('syntara::user.user-informations')
            </div>
        </div>
    </div>
</div>
<?php

function getTzOptions($selectedzone)
{
  $all = timezone_identifiers_list();

  if (empty($selectedzone)) {
    $selectedzone = 'UTC';
  }

  $i = 0;
  foreach ($all AS $zone) {
    $zone = explode('/', $zone);
    $zonen[$i]['continent'] = isset($zone[0]) ? $zone[0] : '';
    $zonen[$i]['city'] = isset($zone[1]) ? $zone[1] : '';
    $zonen[$i]['subcity'] = isset($zone[2]) ? $zone[2] : '';
    $i++;
  }

  asort($zonen);

  $structure = '';

  foreach ($zonen AS $zone) {
    extract($zone);
    if ($continent == 'Africa' || $continent == 'America' || $continent == 'Antarctica' || $continent == 'Arctic' || $continent == 'Asia' || $continent == 'Atlantic' || $continent == 'Australia' || $continent == 'Europe' || $continent == 'Indian' || $continent == 'Pacific') {
      if (!isset($selectcontinent)) {
        $structure .= '<optgroup label="' . $continent . '">'; // continent
      }
      elseif ($selectcontinent != $continent) {
        $structure .= '</optgroup><optgroup label="' . $continent . '">'; // continent
      }

      if (isset($city) != '') {
        if (!empty($subcity) != '') {
          $city = $city . '/' . $subcity;
        }
        $structure .= "<option " . ((($continent . '/' . $city) == $selectedzone) ? 'selected="selected "' : '') . " value=\"" . ($continent . '/' . $city) . "\">" . str_replace('_', ' ', $city) . "</option>"; //Timezone
      }
      else {
        if (!empty($subcity) != '') {
          $city = $city . '/' . $subcity;
        }
        $structure .= "<option " . (($continent == $selectedzone) ? 'selected="selected "' : '') . " value=\"" . $continent . "\">" . $continent . "</option>"; //Timezone
      }

      $selectcontinent = $continent;
    }
  }
  $structure .= '</optgroup>';
  return $structure;
}

?>
@stop
