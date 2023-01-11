<nav class="navbar navbar-expand-lg bg-info px-4 mb-4">
    <div class="container-fluid d-flex flex-row">
        @if (Auth::user())
            <div class="d-flex flex-row">
                @if (Auth::user()->admin == 1)
                    <a class="navbar-brand" href="{{ route('home', "all") }}">
                        <img class="me-3" src="/Images/LogoLoto.png">
                    </a>
                @elseif (Auth::user()->admin == 0)
                    <a class="navbar-brand" href="{{ route('home', Auth::user()->id) }}">Home</a>
                @endif
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @if (Auth::user()->admin == 1)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropUsers" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Participants
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropUsers">
                                <li>
                                    <button type="button" class="btn mt-2 dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#modalAddParticipant">
                                        Ajouter
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn mt-2 dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#modalJouer">
                                        Jouer
                                    </button>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropGains" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Gains
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropGains">
                            <li>
                                <a class="dropdown-item" href="{{ route('getGainHistory') }}">Gains</a>
                            </li>
                            @if ((Auth::user()->admin == 1))
                                <li>
                                    <button type="button" class="btn mt-2 dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#modalAddGain">
                                        Ajouter Gain
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </li>

                    @if ((Auth::user()->admin == 1))
                        <li class="nav-item dropdown me-5">
                            <a class="nav-link dropdown-toggle" href="#" id="dropUsers" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Groupes
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropUsers">
                                <li>
                                    <button type="button" class="btn mt-2 dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#modalAddGroup">
                                        Créer Groupe
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn mt-2 dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#modalParticipantGroup">
                                        Gérer Groupes
                                    </button>
                                </li>
                            </ul>
                        </li> 
                    @endif
                    
                    @if ((Auth::user()->admin == 1))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropFonds" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Fonds
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropUsers">
                                <li>
                                    <table class="mx-4">
                                        <tbody class="text-nowrap">
                                            @foreach ( $fonds as $fond )
                                                <tr>
                                                    <td><h3 class="me-3 fw-bold">{{ $fond['nameGroup'] }}</h3></td>
                                                    <td class="text-end"><h3 class="text-info fw-bold">
                                                        @if ( $fond['fonds'] < 0)
                                                            <p class="text-danger mb-0"> {{ $fond['fonds'] }} €</p>
                                                        @else
                                                            <p class="mb-0"> {{ $fond['fonds'] }} €</p>
                                                        @endif
                                                    </h3></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropGainsGroups" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Gains Résumé
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropGainsGroups">
                            <li>
                                <table class="mx-4">
                                    <tbody class="text-nowrap">
                                        @foreach ( $sommeGainsByGroups as $sommeGainsByGroup )
                                            <tr>
                                                <td><h3 class="me-3 fw-bold">{{ $sommeGainsByGroup['nameGroup'] }}</h3></td>
                                                @if ( $sommeGainsByGroup['sommeGains'] > 0.00)
                                                    <td class="text-end"><h3 class="text-info fw-bold"> {{ $sommeGainsByGroup['sommeGains'] }} €</h3></td>
                                                @endif    
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </li>
                        </ul>
                    </li>
                </ul>

                @if (Auth::user()->admin == 1)
                    <form class="me-3 mb-2" action="{{ route('searchParticipant') }}" method="GET">
                        @csrf
                        <div class="input-group flex-nowrap ">
                            <input class="form-control me-1 mt-2" maxlength="15" name="inputParticipant" placeholder="Participant..."
                                aria-label="Participant...">
                            <button class="btn btn-outline-success ms-1 mt-2 ui-tooltip" title="chercher" type="submit">🔎</button>
                        </div>
                    </form>
                @endif
                
            </div>

            <div class="d-flex flex-row">
                @if (!Auth::user()) 
                    <input type="submit" class="form-control me-2 btn btnhover"
                        value="Visiteur"
                        readonly
                    >
                @else
                    <a class="nav-link text-white ms-1 dropdown-item me-3" href="#" id="userPages" role="button">
                        <b>
                            <form action="{{ route('participant', Auth::user()->id) }}" method="get">
                                <input type="submit" class="form-control me-2 btn btnhover"
                                    name="inputDetailUser" 
                                    value=
                                        @if (Auth::user()->admin == 1)
                                            "👑  {{ Auth::user()->pseudo }}"
                                        @else
                                            {{ Auth::user()->pseudo }} 
                                        @endif
                                    readonly
                                >
                            </form>
                        </b>
                    </a>
                    <a class="btn ui-tooltip" title="logout" href="{{ route('logout') }}">🚪</a>
                @endif
            </div>
            
        @else
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <img class="me-3" src="/Images/LogoLoto.png">
                <h2>The Loto experience in group</h2> 
            </div>
        @endif 
    </div>
</nav>