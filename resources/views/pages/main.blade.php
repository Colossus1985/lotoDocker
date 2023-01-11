@extends('layouts.home')
@section('content')
<div class="px-5">
    <div class="mt-0">
        <table class="table table-bordered">
            <tr class="bg-light text-center fs-4">
                <th>Groupe</th>
                <th>Pseudo</th>
                <th>Disponible</th>
                <th>Joué</th>
            </tr>
            @foreach ($participants as $participant)
                <tr>
                    @if ($participant->nameGroup == null || $participant->nameGroup == "" || $participant->nameGroup == "null")
                        <td class="text_nowrap align-middle d-flex align-items-center justify-content-center">pas de groupe</td>
                    @else
                        <td class="text_nowrap align-middle d-flex align-items-center justify-content-center">{{ $participant->nameGroup }}</td>
                    @endif
                    <td>
                        <form action="{{ route('participant', $participant->id) }}" method="get">
                            <input type="submit" class="form-control me-2 btn btnhover ui-tooltip" title="voir détails"
                                name="inputDetailUser" value="{{ $participant->pseudo }}" readonly>
                        </form>
                    </td>
    
                    @if ( $participant->amount < 0)
                        @if (Auth::user()->admin == 1)
                            <td class="bg-dark fw-bold text-end text-white pe-3 align-middle d-flex align-items-center justify-content-between">
                                <div class="d-flex flex-row">
                                    <button type="button" class="btn btn-light me-3 border ui-tooltip" title="crediter" data-bs-toggle="modal" data-bs-target="#modalAddMoney{{$participant->id}}">
                                        ➕</button>
                                    <button type="button" class="btn btn-light me-3 border ui-tooltip" title="debiter" data-bs-toggle="modal" data-bs-target="#modalDebitMoney{{$participant->id}}">
                                        ➖</button>
                                </div>
                                {{ number_format($participant->amount, 2) }} €
                            </td>
                        @else
                            <td class="bg-dark text-white fw-bold text-end pe-3 align-middle">{{ number_format($participant->amount, 2) }} €</td>
                        @endif
                    @elseif ( $participant->amount == null || $participant->amount == 0)
                        @if (Auth::user()->admin == 1)
                            <td class="bg-light fw-bold text-end pe-3 align-middle d-flex align-items-center justify-content-between">
                                    <div class="d-flex flex-row">
                                        <button type="button" class="btn btn-light me-3 border ui-tooltip" title="crediter" data-bs-toggle="modal" data-bs-target="#modalAddMoney{{$participant->id}}">
                                            ➕</button>
                                        <button type="button" class="btn btn-light me-3 border ui-tooltip" title="debiter" data-bs-toggle="modal" data-bs-target="#modalDebitMoney{{$participant->id}}">
                                            ➖</button>
                                    </div>
                                0.00 €
                            </td>
                        @else
                            <td class="bg-light fw-bold text-end pe-3 align-middle">0.00 €</td>
                        @endif
                    @elseif ( $participant->amount <= 3.49)
                        @if (Auth::user()->admin == 1)
                            <td class="bg-danger fw-bold text-end pe-3 align-middle d-flex align-items-center justify-content-between">
                                <div class="d-flex flex-row">
                                    <button type="button" class="btn btn-light me-3 border ui-tooltip" title="crediter" data-bs-toggle="modal" data-bs-target="#modalAddMoney{{$participant->id}}">
                                        ➕</button>
                                    <button type="button" class="btn btn-light me-3 border ui-tooltip" title="debiter" data-bs-toggle="modal" data-bs-target="#modalDebitMoney{{$participant->id}}">
                                        ➖</button>
                                </div>
                                {{ number_format($participant->amount, 2) }} €
                            </td>
                        @else
                            <td class="bg-danger fw-bold text-end pe-3 align-middle">{{ number_format($participant->amount, 2) }} €</td>
                        @endif
                    @elseif ( $participant->amount < 10 && $participant->amount >= 3.5)
                        @if (Auth::user()->admin == 1)
                            <td class="bg-warning fw-bold text-end pe-3 align-middle d-flex align-items-center justify-content-between">
                                <div class="d-flex flex-row">
                                    <button type="button" class="btn btn-light me-3 border ui-tooltip" title="crediter" data-bs-toggle="modal" data-bs-target="#modalAddMoney{{$participant->id}}">
                                        ➕</button>
                                    <button type="button" class="btn btn-light me-3 border ui-tooltip" title="debiter" data-bs-toggle="modal" data-bs-target="#modalDebitMoney{{$participant->id}}">
                                        ➖</button>
                                </div>
                                {{ number_format($participant->amount, 2) }} €
                            </td>
                        @else
                            <td class="bg-warning fw-bold text-end pe-3 align-middle">{{ number_format($participant->amount, 2) }} €</td>
                        @endif
                    @elseif ( $participant->amount >= 10)
                        @if (Auth::user()->admin == 1)
                            <td class="bg-success fw-bold text-end pe-3 align-middle d-flex align-items-center justify-content-between">
                                <div class="d-flex flex-row">
                                    <button type="button" class="btn btn-light me-3 border btnhover" data-bs-toggle="modal" data-bs-target="#modalAddMoney{{$participant->id}}">
                                        ➕</button>
                                    <button type="button" class="btn btn-light me-3 border btnhover" data-bs-toggle="modal" data-bs-target="#modalDebitMoney{{$participant->id}}">
                                        ➖</button>
                                </div>
                                {{ number_format($participant->amount, 2) }} €
                            </td>
                        @else
                            <td class="bg-success fw-bold text-end pe-3 align-middle">{{ number_format($participant->amount, 2) }} €</td>
                        @endif
                    @endif
                    <td class="align-middle text-end fw-bold">{{ number_format($participant->totalAmount, 2) }} €
                </tr>
                @include('modals.addMoney')
                @include('modals.debitMoney')
            @endforeach
        </table>
    </div>
</div>
@endsection