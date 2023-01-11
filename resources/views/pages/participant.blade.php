@extends('layouts.home')
@section('content')
<div class="mx-5 fw-bold">
    <form method="POST" action="{{ route('updateParticipant', $participant[0]->id) }}">
        @csrf
        <div class="d-flex flex-row">
            <div class="form-group form-floating mb-3 me-3 d-flex flex-fill">
                <input id="floatingfirstName" type="text" class="form-control flex-fill fw-bold" name="inputFirstName"
                    value="{{ $participant[0]->firstName }}"
                    @if (Auth::user()->id != $participant[0]->id)
                        readonly
                    @endif
                    >
                <label for="floatingfirstName" class="text-nowrap">Prenom</label>
            </div>

            <div class="form-group form-floating mb-3 me-3 d-flex flex-fill">
                <input id="floatinglastName" type="text" class="form-control flex-fill fw-bold" name="inputLastName"
                    value="{{ $participant[0]->lastName }}"
                    @if (Auth::user()->id != $participant[0]->id)
                        readonly
                    @endif
                    >
                <label for="floatinglastName" class="text-nowrap">Nom</label>
            </div>

            <div class="form-group form-floating mb-3 d-flex flex-fill">
                <input id="floatingpseudo" type="text" class="form-control flex-fill fw-bold" name="inputPseudo"
                    value="{{ $participant[0]->pseudo }}"
                    @if (Auth::user()->id != $participant[0]->id)
                        readonly
                    @endif
                    >
                <label for="floatingpseudo" class="text-nowrap">Pseudo</label>
            </div>
        </div>
        
        <div class="d-flex flex-row">
            <div class="form-group form-floating mb-3 me-3">
                <input id="floatingTel" type="text" class="form-control fw-bold" name="inputTel"
                    value="{{ $participant[0]->tel }}"
                    @if (Auth::user()->id != $participant[0]->id)
                        readonly
                    @endif
                    >
                <label for="floatingTel" class="text-nowrap">Téléphone</label>
            </div>
            <div class="form-group form-floating mb-3 me-3 d-flex flex-fill">
                <input id="floatingEmail" type="email" class="form-control flex-fill fw-bold" name="inputEmail"
                    value="{{ $participant[0]->email }}"
                    @if (Auth::user()->id != $participant[0]->id)
                        readonly
                    @endif
                    >
                <label for="floatingEmail" class="text-nowrap">Email</label>
            </div>
            <div class="d-flex flex-row mb-3 ">
                <div class="form-group form-floating d-flex flex-fill 
                    @if (Auth::user()->admin == 1 && Auth::user()->id != $participant[0]->id) me-0
                    @else me-3
                    @endif">
                    @if ($participant[0]->nameGroup == null || $participant[0]->nameGroup == "null" || $participant[0]->nameGroup == "") 
                        <input id="floatingNameGroup" type="text" class="form-control flex-fill fw-bold" name="inputNameGroupNew"
                            value="Pas de groupe" readonly>
                        <label for="floatingNameGroup" class="text-nowrap">Groupe</label>
                    @else
                        <input id="floatingNameGroup" type="text" class="form-control flex-fill fw-bold" name="inputNameGroupNew"
                            value="{{ $participant[0]->nameGroup }}" readonly>
                        <label for="floatingNameGroup" class="text-nowrap">Groupe</label>
                    @endif
                </div>
            </div>
            
        </div>
        @if (Auth::user()->id == $participant[0]->id)
            <div class="d-flex flex-row">
                <div class="form-group form-floating mb-3 me-3">
                    <input id="floatingPassword" type="password" maxlength="20" minlength="3" class="form-control ui-tooltip" title="entre 3 et 20 charactères" name="inputPasswordActuel"
                        placeholder="Password" required>
                    <label for="floatingPassword">actuel Password</label>
                </div>
                <div class="form-group form-floating mb-3 me-3">
                    <input id="floatingPasswordNew" type="password" maxlength="20" minlength="3" class="form-control ui-tooltip" title="entre 3 et 20 charactères" name="inputPassword"
                        placeholder="Password">
                    <label for="floatingPasswordNew">Nouveau Password</label>
                </div>
                <div class="form-group form-floating mb-3">
                    <input id="floatingConfirmPassword" type="password" maxlength="20" minlength="3" class="form-control ui-tooltip" title="entre 3 et 20 charactères"
                        name="inputPassword_confirmation" placeholder="Confirm Password">
                    <label for="floatingConfirmPassword">Confirmer Password</label>
                </div>
            </div>
        @endif
        <div class="d-flex flex-row">
            <div class="form-group form-floating mb-3 me-3">
                <input id="floatingAmount" class="form-control text-end fw-bold"
                    value="{{ $participant[0]->amount }} €" readonly>
                <label for="floatingAmount" class="text-nowrap">Disponible</label>
            </div>

            <div class="form-group form-floating mb-3 me-3">
                <input id="floatingAmount" class="form-control text-end fw-bold"
                    value="{{ $participant[0]->totalAmount }} €" readonly>
                <label for="floatingAmount" class="text-nowrap">Joué</label>
            </div>
            
            @if (Auth::user()->id == $participant[0]->id )
                <div class="form-group form-floating flex-fill d-flex mb-3">
                    <button type="submit" class="btn btn-primary text-nowrap flex-fill">Enregistrer Changement</button>
                </div>
            @endif
            
        </div>
    </form>
    @if (Auth::user()->admin == 1)
    <div>
        <form action="{{ route('changeGroup', $participant[0]->id) }}" method = "POST">
            @csrf
            <div class="border border-3 rounded-3 d-flex flex-column  ps-3 py-2 mb-3">
                <div class="">
                    <p class="mt-1 mb-2 text-nowrap">Changer le Groupe : </p>
                </div>
                <div class="d-flex flex-row text-nowrap flex-wrap">
                    <div class="form-check form-switch bg-warning rounded-2 me-3 ps-1  text-nowrap">
                        <input class="form-check-input me-3 ms-0"
                            type="radio" 
                            name="inputNameGroupNew" 
                            role="switch" 
                            id="flexSwitchNameGroup" 
                            value="null">
                        <label class="form-check-label me-2 text-nowrap" for="flexSwitchNameGroup">Pas de groupe</label>
                    </div>
                    @foreach ($groups as $group)
                        <div class="ms-1 form-check form-switch me-3">
                            <input class="form-check-input me-2"
                                type="radio" 
                                name="inputNameGroupNew" 
                                role="switch" 
                                id="flexSwitchNameGroup" 
                                value="{{ $group->nameGroup }}">
                            <label class="form-check-label text-nowrap" for="flexSwitchNameGroup">{{ $group->nameGroup }}</label>
                        </div>
                    @endforeach
                </div>
                <div class="form-group form-floating d-flex mt-3">
                    <button type="submit" class="btn btn-primary text-nowrap">Changer groupe</button>
                </div>
            </div>
        </form>
    </div>
        
     @endif

    <div>
        <table class="table table-bordered my-4">
            <tr class="bg-light text-center">
                <th>Montant</th>
                <th>Credit</th>
                <th>Debit</th>
                <th>Credit Gain</th>
                <th>Date</th>
            </tr>
            @foreach ($actions as $action)
                <tr>
                    @if ($action->amount < 0)
                        <td class="text-end fw-bold bg-dark text-white">
                            {{ number_format($action->amount, 2) }} €
                        </td>
                    @elseif ($action->amount == 0.00 || $action->amount == null)
                        <td class="text-end fw-bold bg-light">
                            {{ number_format($action->amount, 2) }} €
                        </td>
                    @elseif ($action->amount >= 0.01 && $action->amount <= 3.49 )
                        <td class="text-end fw-bold bg-danger">
                            {{ number_format($action->amount, 2) }} €
                        </td>
                    @elseif ($action->amount >= 3.50 && $action->amount <= 9.99)
                        <td class="text-end fw-bold bg-warning">
                            {{ number_format($action->amount, 2) }} €
                        </td>
                    @elseif ($action->amount >= 10.00)
                        <td class="text-end fw-bold bg-success">
                            {{ number_format($action->amount, 2) }} €
                        </td>
                    @endif
                    
                    @if ( $action->credit >= 0.01 )
                        <td class="bg-success text-end fw-bold">{{ number_format($action->credit, 2) }} €</td>
                    @else
                        <td class="text-end fw-bold">{{ number_format($action->credit, 2) }} €</td>
                    @endif

                    @if ( $action->debit >= 0.01 )
                        <td class="bg-danger text-end fw-bold">{{ number_format($action->debit, 2) }} €</td>
                    @else
                        <td class="text-end fw-bold">{{ number_format($action->debit, 2) }} €</td>
                    @endif

                    @if ( $action->creditGain >= 0.01 )
                        <td class="bg-success text-end fw-bold">{{ number_format($action->creditGain, 2) }} €</td>
                    @else
                        <td class="text-end fw-bold">{{ number_format($action->creditGain, 2) }} €</td>
                    @endif

                    <td class="fw-bold text-center">{{ $action->date }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    {{ $actions->links() }}
    

    <form action="{{ route('participantDelete', $participant[0]->id) }}" method="get">
        @csrf
        @method('DELETE')
        <div class="">
            <button type="submit" class="btn btn-danger"
                onclick="return confirm('Veux tu vraiment supprimer {{ $participant[0]->pseudo }} ?');">Supprimer {{$participant[0]->pseudo}}
            </button>
        </div>
    </form>
</div>


@endsection