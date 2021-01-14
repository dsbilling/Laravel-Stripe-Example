<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Cartalyst\Stripe\Exception\CardErrorException;
use Cartalyst\Stripe\Exception\ServerErrorException;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stripe_customer = Auth::user()->stripe_customer;

        if ($stripe_customer) {
            $cards = Stripe::cards()->all($stripe_customer);
            $cards = $cards['data'];
        } else {
            $cards = [];
        }
        return view('card.index')->with('cards', $cards);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('card.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $stripe_customer = auth()->user()->stripe_customer;

        $cardNumber         = str_replace(' ', '', $request->get('number'));
        $cardMonthExpiry    = $request->get('expiryMonth');
        $cardCVC            = $request->get('cvc');
        $cardYearExpiry     = $request->get('expiryYear');
        $nameOnCard         = $request->get('name');

        try {
            $token = Stripe::tokens()->create([
                'card' => [
                    'number'    => $cardNumber,
                    'exp_month' => $cardMonthExpiry,
                    'cvc'       => $cardCVC,
                    'exp_year'  => $cardYearExpiry,
                    'name'      => $nameOnCard,
                ],
            ]);
        } catch (CardErrorException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return redirect()->route('account-billing-card-create')->with('messagetype', 'danger')
                                ->with('error', 'Card Error: '.$message);
        }

        try {
            Stripe::cards()->create($stripe_customer, $token['id']);
        } catch (CardErrorException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return redirect()->route('account-billing-card-create')->with('messagetype', 'danger')
                                ->with('error', $message.'. Please try again.');
        } catch (ServerErrorException $e) {
            // Get the status code
            $code = $e->getCode();

            // Get the error message returned by Stripe
            $message = $e->getMessage();

            // Get the error type returned by Stripe
            $type = $e->getErrorType();

            return redirect()->route('account-billing-card-create')->with('messagetype', 'danger')
                                ->with('error', $message.'. Please try again.');
        }

        return redirect()->route('account-billing-card')->with('messagetype', 'success')
                                ->with('success', 'Card was added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stripe_customer = auth()->user()->stripe_customer;
        abort_unless($stripe_customer, 403);

        Stripe::cards()->delete($stripe_customer, $id);

        return redirect()->route('account-billing-card')->with('messagetype', 'success')
                            ->with('message', __('user.card.alert.deleted'));
    }
}