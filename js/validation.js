(function ($) {
    $(function () {
        var $password = $('#password');
        var $confirmPassword = $('#confirm_password');
        var $note = $('#password-help');

        if (!$password.length || !$confirmPassword.length || !$note.length) {
            return;
        }

        function updateNote() {
            if ($confirmPassword.val() === '') {
                $note.text('Passwords must match.');
                $note.removeClass('note-success note-error');
                return;
            }

            if ($password.val() === $confirmPassword.val()) {
                $note.text('Passwords match.');
                $note.removeClass('note-error').addClass('note-success');
                return;
            }

            $note.text('Passwords do not match.');
            $note.removeClass('note-success').addClass('note-error');
        }

        $password.on('input', updateNote);
        $confirmPassword.on('input', updateNote);
    });
}(window.jQuery));
