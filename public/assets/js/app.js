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

/**
 * Diễn đàn: bảng biểu cảm, chọn file, kiểm tra dung lượng, đếm ngược 30 giây.
 * (Mọi kiểm tra quan trọng vẫn nằm ở server – JS chỉ để người dùng đỡ phải đoán.)
 */
(function () {
  'use strict';
  // Emoji Unicode có sẵn trong mọi trình duyệt; sticker ảnh lấy từ thư mục public/assets/emoji (bạn thêm file là có).
  var EMOJIS = ('😀 😁 😂 🤣 😊 😍 😘 😎 🤔 😅 😭 😡 😱 🥳 😴 🤩 👍 👎 👏 🙏 💪 🔥 ✨ ⭐ ❤️ 💔 💯 🎉 🎁 🏆 ⚔️ 🐉 🍅 🍜 🍚 ☕ 🌸 🌙 ☀️ 😈 👻 💀 🤝 🙌 😏 🥺 😤').split(' ');

  document.querySelectorAll('[data-composer]').forEach(function (form) {
    var ta = form.querySelector('textarea');
    var panel = form.querySelector('[data-emoji-panel]');
    var toggle = form.querySelector('[data-emoji-toggle]');
    var file = form.querySelector('[data-file]');
    var fileName = form.querySelector('[data-file-name]');
    var submit = form.querySelector('[data-submit]');
    var err = form.querySelector('[data-error]');
    var maxBytes = parseInt(form.dataset.maxBytes, 10);
    var label = submit.textContent;
    var stickers = [];
    try { stickers = JSON.parse(form.dataset.stickers || '[]'); } catch (e) {}

    function insert(text) {           // chèn tại vị trí con trỏ
      var s = ta.selectionStart || 0, e = ta.selectionEnd || 0;
      ta.value = ta.value.slice(0, s) + text + ta.value.slice(e);
      ta.focus(); ta.selectionStart = ta.selectionEnd = s + text.length;
    }
    function showError(msg) { err.textContent = msg; err.hidden = !msg; }

    // ---- bảng biểu cảm (dựng một lần, khi mở lần đầu)
    var built = false;
    toggle.addEventListener('click', function () {
      if (!built) {
        EMOJIS.forEach(function (ch) {
          var b = document.createElement('button'); b.type = 'button'; b.className = 'emoji-btn'; b.textContent = ch;
          b.addEventListener('click', function () { insert(ch); });
          panel.appendChild(b);
        });
        stickers.forEach(function (st) {
          var b = document.createElement('button'); b.type = 'button'; b.className = 'emoji-btn emoji-btn--img'; b.title = ':' + st.name + ':';
          var im = document.createElement('img'); im.src = st.src; im.alt = ':' + st.name + ':'; im.width = 28; im.height = 28;
          b.appendChild(im); b.addEventListener('click', function () { insert(':' + st.name + ': '); });
          panel.appendChild(b);
        });
        built = true;
      }
      panel.hidden = !panel.hidden;
      toggle.setAttribute('aria-expanded', String(!panel.hidden));
    });

    // ---- chọn file: kiểm tra dung lượng ngay để khỏi chờ tải lên rồi mới báo lỗi
    file.addEventListener('change', function () {
      showError('');
      var f = file.files[0];
      if (!f) { fileName.textContent = ''; return; }
      if (f.size > maxBytes) { showError('File quá lớn (tối đa ' + form.dataset.maxMb + 'MB).'); file.value = ''; fileName.textContent = ''; return; }
      fileName.textContent = f.name + ' (' + (f.size / 1048576).toFixed(1) + 'MB)';
    });

    // ---- đếm ngược thời gian chờ (đăng bài 5 phút, bình luận 15 giây)
    var wait = parseInt(form.dataset.wait, 10) || 0;
    function tick() {
      if (wait > 0) { submit.disabled = true; submit.textContent = 'Chờ ' + (wait >= 60 ? Math.floor(wait / 60) + ':' + ('0' + (wait % 60)).slice(-2) : wait + 's'); wait--; setTimeout(tick, 1000); }
      else { submit.disabled = false; submit.textContent = label; }
    }
    tick();

    form.addEventListener('submit', function (ev) {
      if (!ta.value.trim() && !file.files.length) { ev.preventDefault(); showError('Hãy nhập nội dung hoặc đính kèm ảnh/video.'); return; }
      submit.disabled = true; submit.textContent = 'Đang gửi...';   // tránh bấm hai lần
    });
  });
})();
