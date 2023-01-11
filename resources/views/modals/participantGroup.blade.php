<div class="modal fade" id="modalParticipantGroup" tabindex="-1" aria-labelledby="addGain" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex flex-row">
                    <h5 class="modal-title me-3" id="addGain">
                        🥳🥳🥳🥳 Gérer les Groupe 🥳🥳🥳🥳
                    </h5>
                </div>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('participantGroup') }}">
                    @csrf
                    <div class="form-group form-floating mb-3 d-flex flex-row">
                        <div class="border border-3 rounded-3 form-group form-floating me-3 d-flex flex-fill flex-column">
                            <div class="">
                                <p class="mt-1 mb-2 ps-3">Choisis le Group : </p>
                            </div>
                             @foreach ($groups as $group)
                                <div class="ms-3 form-check form-switch">
                                    <input class="form-check-input me-3"
                                        type="radio" 
                                        name="inputNameGroup" 
                                        role="switch" 
                                        id="flexSwitchNameGroup" 
                                        value="{{ $group->nameGroup }}">
                                    <label class="form-check-label" for="flexSwitchNameGroup">{{ $group->nameGroup }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="border border-3 rounded-3 form-group form-floating d-flex flex-fill flex-column">
                            <div class="">
                                <p class="mt-1 mb-2 ps-3">Choisir le(s) Participant(s) : </p>
                            </div>
                            @foreach ($participants as $participant)
                                <div class="ms-3 form-check form-switch">
                                    <input class="form-check-input me-3"
                                        type="checkbox" 
                                        name="inputParticipantArray[]" 
                                        role="switch" 
                                        id="flexSwitchCheckDefault" 
                                        value="{{ $participant->pseudo }}">
                                    <label class="form-check-label" for="flexSwitchCheckDefault">{{ $participant->pseudo}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex btn-G-L justify-content-end">
                        <button
                            class="btn btn-primary"
                            type="submit"
                            style="width: 45%"
                            onclick="return confirm('Sur de la composition du group?');"
                        >
                            Composer le group
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-start">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
    <script src="/js/addGain.js"></script>
</div>
