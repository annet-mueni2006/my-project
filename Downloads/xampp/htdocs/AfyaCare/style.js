document.addEventListener('DOMContentLoaded', () => {
  const password = document.querySelector('input[name="password"]');
  if (password && password.autocomplete === 'new-password') password.minLength = 8;
  const otp = document.querySelector('input[name="otp"]');
  if (otp) { otp.inputMode = 'numeric'; otp.maxLength = 6; otp.pattern = '[0-9]{6}'; }
});
