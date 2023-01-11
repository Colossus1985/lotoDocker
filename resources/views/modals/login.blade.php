<div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLogin" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLogin">Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('login.action') }}">
                    @csrf
                    <div class="d-flex form-group form-floating mb-3">
                        <input type="text" class="form-control flex-fill" name="inputRegister" value="{{ old('inputRegister') }}" 
                        placeholder="Pseudo or E-mail" id="loginFloatingName">
                        <label for="loginFloatingName">Pseudo or E-mail</label>
                    </div>
                    
                    <div class="d-flex form-group form-floating mb-3">
                        <input type="password" class="form-control flex-fill" name="inputPassword" placeholder="Password"  id="loginFloatingPassword">
                        <label for="loginFloatingPassword">Password</label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit" style="width:45%;">Confirmer</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-start">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
