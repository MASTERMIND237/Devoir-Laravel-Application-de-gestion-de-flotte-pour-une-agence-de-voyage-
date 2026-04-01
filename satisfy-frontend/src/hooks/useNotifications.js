import toast from 'react-hot-toast';

export const useNotifications = () => {
  const notifySuccess = (message) => toast.success(message, {
    style: { border: '1px solid #89e900', padding: '16px', color: '#004643' },
    iconTheme: { primary: '#89e900', secondary: '#fff' },
  });

  const notifyError = (message) => toast.error(message, {
    style: { border: '1px solid #ef4444', padding: '16px', color: '#7f1d1d' },
  });

  const notifyInfo = (message) => toast(message, {
    icon: 'ℹ️',
    style: { background: '#004643', color: '#fff' },
  });

  return { notifySuccess, notifyError, notifyInfo };
};