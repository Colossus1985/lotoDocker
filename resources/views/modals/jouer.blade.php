<div class="modal fade" id="modalJouer" tabindex="-1" aria-labelledby="jouer" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-center">
                    <h5 class="modal-title me-3" id="jouer">
                        🍀🍀🍀🍀 Miser pour jouer 🍀🍀🍀🍀
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
                <form method="POST" action="{{ route('debitAll') }}">
                    @csrf
                    <div class="d-flex flex-row">
                        <div class="form-group form-floating mb-3 me-3 d-flex flex-column">
                            <div class="form-group form-floating mb-3 d-flex flex-fill">
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="form-control flex-fill"
                                    name="inputAmount"
                                    id="floatingMontant"
                                    value="{{ old('inputAmount') }}"
                                    placeholder="Montant ➖ €"
                                    required
                                />
                                <label for="floatingMontant">Montant ➖ <span>€</span></label>
                            </div>
                            <div class="form-group form-floating d-flex flex-fill">
                                <input
                                    type="date"
                                    class="form-control flex-fill"
                                    name="inputDate"
                                    id="floatingDate"
                                    value="<?php echo (new DateTime())->format('Y-m-d'); ?>"
                                    placeholder="gain"
                                    required
                                />
                                <label for="floatingDate">Date</label>
                            </div>
                        </div>
                        
                        
                        <div class="border border-3 rounded-3 form-group form-floating mb-3 d-flex flex-fill flex-column">
                            <div class="">
                                <p class="mt-1 mb-2 ps-3">Choisis le Group : </p>
                            </div>
                             @foreach ($groupsDispo as $group)
                                <div class="ms-3 form-check form-switch">
                                    <input class="form-check-input me-3"
                                        type="radio" 
                                        name="inputNameGroup" 
                                        role="switch" 
                                        id="flexSwitchNameGroup" 
                                        value="{{ $group->nameGroup }}"
                                        required
                                    />
                                    <label class="form-check-label" for="flexSwitchNameGroup">{{ $group->nameGroup }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex btn-G-L d-flex justify-content-end">
                        <button
                            class="btn btn-primary"
                            type="submit"
                            style="width: 50%"
                            onclick="return confirm('Jouer et retirer la mise de tous les participant(s)?');"
                        >
                            Jouer
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
