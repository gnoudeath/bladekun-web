/**
 * app.js – JavaScript nhỏ cho giao diện. Web vẫn dùng được khi tắt JavaScript;
 * file này chỉ thêm 1 tiện ích:
 *   - Nút "Hiện/Ẩn" mật khẩu trong các form (menu điện thoại do Bootstrap xử lý)
 */
(function () {
  'use strict';

  // (Menu trên điện thoại do Bootstrap lo, không cần code ở đây.)
  // Nút Hiện / ẩn mật khẩu: nút có thuộc tính data-toggle-password trỏ tới id của ô nhập.
  document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
    button.addEventListener('click', function () {
      var input = document.getElementById(button.getAttribute('data-toggle-password'));
      if (!input) { return; }
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      button.textContent = show ? 'Ẩn' : 'Hiện';
    });
  });
})();
