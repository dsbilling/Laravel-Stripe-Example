@extends('layouts.main')
@section('title', __('card.title'))
@section('content')

<div class="container">
    <div class="page-header">
        <h4 class="page-title">{{ __('card.title') }} <a class="btn btn-sm btn-success ml-2" href="{{ route('card-create') }}"><i class="fa fa-plus"></i> {{ __('global.add') }}</a></h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('header.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('card.title') }}</li>
        </ol>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @if(count($cards) == 0)
                    <div class="card-body">
                        <p><em>{{ __('global.nodata') }}</em></p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <th></th>
                                <th>{{ __('global.payment.cardbrand') }}</th>
                                <th>{{ __('global.payment.cardtype.title') }}</th>
                                <th>{{ __('global.payment.cardname') }}</th>
                                <th>{{ __('global.payment.cardnumber') }}</th>
                                <th>{{ __('global.payment.cardexp') }}</th>
                                <th>{{ __('global.details') }}</th>
                            </thead>
                            <tbody>
                                @foreach($cards as $card)
                                    <tr>
                                        <td>@if(array_search($card, $cards) == 0) <span class="badge badge-default">{{ __('global.default') }}</span> @endif</td>
                                        <td>{{ $card['brand'] }}</td>
                                        <td>{{ __('global.payment.cardtype.'.$card['funding']) }}</td>
                                        <td>{{ $card['name'] }}</td>
                                        <td>&#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; {{ $card['last4'] }}</td>
                                        <td>{{ $card['exp_month'] }} / {{ $card['exp_year'] }}</td>
                                        <td>
                                            <a href="{{ route('card-destroy', $card['id']) }}" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> {{ __('global.delete') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop