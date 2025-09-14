const showIconLoading = (disableBtnEl) => {
  disableBtnEl.prop("disabled", true);
  disableBtnEl.find("span").html('<i class="fa fa-spinner fa-spin"></i>');
};

const showIcon = (enableBtnEl, iconHtml) => {
  enableBtnEl.prop("disabled", false);
  enableBtnEl.find("span").html(iconHtml);
};

const Toast = Swal.mixin({
  toast: true,
  position: "bottom-end", // Position of the toast (e.g., 'top-end', 'bottom-start')
  showConfirmButton: false, // Hide the confirm button
  timer: 3000, // Duration in milliseconds before the toast auto-closes
  timerProgressBar: true, // Show a progress bar for the timer
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer; // Pause timer on hover
    toast.onmouseleave = Swal.resumeTimer; // Resume timer on mouse leave
  },
});

const showMessage = (data) => {
  if (data.status === "success") {
    if (data?.isToast) {
      Toast.fire({
        icon: data.status,
        title: data.msg,
      });
    } else {
      Swal.fire({
        icon: data.status,
        title: data.title ?? "Thành công",
        text: data.msg,
        confirmButtonText: "OK",
      });
    }
  } else {
    if (data?.isToast) {
      Toast.fire({
        icon: data.status,
        title: data.msg,
      });
    } else {
      Swal.fire({
        icon: data.status,
        title: data.title ?? "Lỗi",
        text: data.msg,
        confirmButtonText: "Thử lại",
      });
    }
  }
};
