<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CardRequest;
use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Models\CardField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = QueryBuilder::for(Card::class)
            ->where('user_id', auth()->id())
            ->paginate($request->input('page_size', 10));

        return CardResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CardRequest $request)
    {
        $card = Card::create([
            'id' => Str::uuid()->toString(),
            'user_id' => auth()->id(),
            'label' => $request->input('label')
        ]);

        foreach ($request->input('fields') as $field) {
            CardField::create([
                'card_id' => $card->id,
                'type_id' => $field['type_id'],
                'value' => $field['value'],
                'label' => $field['label']
            ]);
        }

        return CardResource::make($card->loadMissing('fields'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        //
    }
}