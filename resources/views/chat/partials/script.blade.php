<script>
(function () {
  const messagesEl = document.getElementById('messages');
  if (!messagesEl) return;

  const form = document.getElementById('chat-form');
  const input = document.getElementById('chat-body');
  const sendBtn = document.getElementById('chat-send');
  const errBox = document.getElementById('chat-error');
  const attachmentInput = document.getElementById('chat-attachment');
  const attachmentBtn = document.getElementById('attachment-btn');
  const attachmentChip = document.getElementById('chat-attachment-chip');
  const attachmentName = document.getElementById('chat-attachment-name');
  const attachmentRemove = document.getElementById('chat-attachment-remove');
  const emojiToggle = document.getElementById('emoji-toggle');
  const emojiPicker = document.getElementById('emoji-picker');
  const quickReplyButtons = document.querySelectorAll('.quick-reply');
  const typingText = document.getElementById('chat-typing-text');
  const loadMoreBtn = document.getElementById('chat-load-more');
  const searchForm = document.getElementById('chat-search-form');
  const searchInput = document.getElementById('chat-search-input');
  const searchResults = document.getElementById('chat-search-results');
  const searchList = document.getElementById('chat-search-list');
  const searchSummary = document.getElementById('chat-search-summary');
  const searchClearBtn = document.getElementById('chat-search-clear');
  const searchCloseBtn = document.getElementById('chat-search-close');
  const notifyBtn = document.getElementById('chat-notify-btn');
  const presenceDot = document.getElementById('chat-presence-dot');
  const recordBtn = document.getElementById('record-audio-btn');
  const recordingIndicator = document.getElementById('recording-indicator');

  const conversationId = messagesEl.dataset.conversation;
  const meId = Number(messagesEl.dataset.me || 0);
  const meName = messagesEl.dataset.meName || 'Tú';
  const otherId = Number(messagesEl.dataset.other || 0);
  const otherName = messagesEl.dataset.otherName || 'Usuario';
  const readUrl = messagesEl.dataset.readUrl;
  const messagesUrl = messagesEl.dataset.messagesUrl;
  let hasMore = messagesEl.dataset.hasMore === '1';
  let oldestId = Number(messagesEl.dataset.oldestId || 0);
  const csrfToken = '{{ csrf_token() }}';

  const defaultStatusText = typingText ? typingText.textContent : '';
  let presenceState = defaultStatusText || `${otherName} estuvo en línea recientemente`;
  let typingTimeout = null;
  let typingThrottle = null;
  let typingActive = false;

  let notificationsAllowed = typeof Notification !== 'undefined' && Notification.permission === 'granted';
  let notificationsMuted = false;

  let recordedFile = null;
  let mediaRecorder = null;
  let mediaStream = null;
  let recordedChunks = [];

  const scrollBottom = () => {
    messagesEl.scrollTop = messagesEl.scrollHeight;
  };

  const refreshOldestFromDom = () => {
    const bubble = messagesEl.querySelector('.message-bubble');
    oldestId = bubble ? Number(bubble.dataset.messageId) : 0;
    messagesEl.dataset.oldestId = oldestId || '';
  };

  const updateLoadMoreVisibility = (loading = false) => {
    if (!loadMoreBtn) return;
    loadMoreBtn.hidden = !hasMore;
    loadMoreBtn.disabled = loading;
    loadMoreBtn.textContent = loading ? 'Cargando…' : 'Cargar mensajes anteriores';
    messagesEl.dataset.hasMore = hasMore ? '1' : '0';
  };

  refreshOldestFromDom();
  updateLoadMoreVisibility();
  scrollBottom();

  const setSending = (state) => {
    if (!sendBtn) return;
    sendBtn.disabled = state;
    sendBtn.textContent = state ? 'Enviando…' : 'Enviar';
  };

  const showError = (text) => {
    if (!errBox) return;
    errBox.textContent = text;
    errBox.style.display = 'block';
    setTimeout(() => { errBox.style.display = 'none'; }, 3500);
  };

  const autoResize = () => {
    if (!input) return;
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 180) + 'px';
  };

  autoResize();

  const setPresenceState = (text, isOnline = false) => {
    presenceState = text || defaultStatusText || `${otherName} estuvo en línea recientemente`;
    if (!typingActive && typingText) {
      typingText.textContent = presenceState;
    }
    if (presenceDot) {
      presenceDot.classList.toggle('is-online', !!isOnline);
    }
  };

  setPresenceState(presenceState, false);

  const getAttachmentFromMessage = (msg) => {
    if (msg.attachment) return msg.attachment;
    if (msg.attachment_url) {
      return {
        url: msg.attachment_url,
        name: msg.attachment_name,
        type: msg.attachment_type,
      };
    }
    return null;
  };

  const renderAttachment = (attachment) => {
    if (!attachment?.url) return null;
    const type = attachment.type || '';
    const isImage = type.startsWith('image/');
    const isAudio = type.startsWith('audio/');
    const isVideo = type.startsWith('video/');

    const container = document.createElement('div');
    container.className = 'message-attachment' + (isImage ? ' message-attachment--image' : '');

    if (isImage) {
      const link = document.createElement('a');
      link.href = attachment.url;
      link.target = '_blank';
      link.rel = 'noopener';
      link.setAttribute('aria-label', 'Abrir adjunto');

      const img = document.createElement('img');
      img.src = attachment.url;
      img.alt = attachment.name || 'Adjunto';
      img.loading = 'lazy';
      link.appendChild(img);
      container.appendChild(link);
      return container;
    }

    if (isAudio) {
      const audio = document.createElement('audio');
      audio.controls = true;
      audio.preload = 'none';
      const source = document.createElement('source');
      source.src = attachment.url;
      if (type) source.type = type;
      audio.appendChild(source);
      audio.append('Tu navegador no soporta audio embebido.');
      container.appendChild(audio);
      return container;
    }

    if (isVideo) {
      const video = document.createElement('video');
      video.controls = true;
      video.preload = 'metadata';
      const source = document.createElement('source');
      source.src = attachment.url;
      if (type) source.type = type;
      video.appendChild(source);
      video.append('Tu navegador no soporta video embebido.');
      container.appendChild(video);
      return container;
    }

    const link = document.createElement('a');
    link.href = attachment.url;
    link.target = '_blank';
    link.rel = 'noopener';
    link.setAttribute('aria-label', 'Abrir adjunto');

    const icon = document.createElement('span');
    icon.className = 'attachment-icon';
    icon.textContent = '📎';
    const text = document.createElement('span');
    text.textContent = attachment.name || 'Archivo adjunto';
    link.append(icon, text);
    container.appendChild(link);
    return container;
  };

  const formatTime = (value) => {
    if (!value) return '';
    try {
      return new Intl.DateTimeFormat('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: '2-digit',
      }).format(new Date(value));
    } catch (error) {
      return '';
    }
  };

  const removeEmptyState = () => {
    const empty = messagesEl.querySelector('.chat-thread__empty');
    if (empty) empty.remove();
  };

  const renderMessage = (msg) => {
    if (!msg) return null;
    const senderId = Number(msg.sender?.id ?? msg.sender_id ?? 0);
    const isMine = senderId === meId;
    const author = isMine ? meName : (msg.sender?.name || otherName);

    const wrapper = document.createElement('div');
    wrapper.className = `message-bubble ${isMine ? 'message-out' : 'message-in'}`;
    if (msg.id) {
      wrapper.dataset.messageId = msg.id;
    }
    if (isMine) {
      wrapper.dataset.messageStatus = msg.read_at ? 'read' : 'sent';
    }

    const meta = document.createElement('div');
    meta.className = 'message-meta';

    const authorNode = document.createElement('span');
    authorNode.className = 'message-author';
    authorNode.textContent = author;

    const timeNode = document.createElement('span');
    timeNode.className = 'message-time';
    timeNode.textContent = formatTime(msg.created_at) || 'Ahora';

    meta.append(authorNode, timeNode);
    wrapper.append(meta);

    const attachment = getAttachmentFromMessage(msg);
    const attachmentNode = renderAttachment(attachment);
    if (attachmentNode) {
      wrapper.append(attachmentNode);
    }

    if (msg.body) {
      const text = document.createElement('div');
      text.className = 'message-text';
      text.textContent = msg.body;
      wrapper.append(text);
    }

    const footer = document.createElement('div');
    footer.className = 'message-footer';

    const timeFooter = document.createElement('span');
    timeFooter.className = 'message-time';
    timeFooter.textContent = formatTime(msg.created_at) || 'Ahora';
    footer.append(timeFooter);

    if (isMine) {
      const status = document.createElement('span');
      status.className = 'message-status' + (msg.read_at ? ' is-read' : '');
      status.textContent = msg.read_at ? 'Visto' : 'Enviado';
      footer.append(status);
    }

    wrapper.append(footer);
    return wrapper;
  };

  const highlightBubble = (bubble) => {
    if (!bubble) return;
    bubble.classList.add('message-bubble--highlight');
    setTimeout(() => bubble.classList.remove('message-bubble--highlight'), 2600);
  };

  const appendMessage = (msg) => {
    const node = renderMessage(msg);
    if (!node) return;
    removeEmptyState();
    messagesEl.appendChild(node);
    scrollBottom();

    const senderId = Number(msg.sender?.id ?? msg.sender_id ?? 0);
    if (msg.id && senderId !== meId) {
      queueRead(msg.id);
      triggerBrowserNotification(msg);
    }
  };

  const prependMessages = (list = []) => {
    if (!Array.isArray(list) || !list.length) return;
    const prevHeight = messagesEl.scrollHeight;
    const fragment = document.createDocumentFragment();
    list.forEach((msg) => {
      const node = renderMessage(msg);
      if (node) fragment.appendChild(node);
    });
    const firstBubble = messagesEl.querySelector('.message-bubble');
    if (firstBubble) {
      messagesEl.insertBefore(fragment, firstBubble);
    } else {
      messagesEl.appendChild(fragment);
    }
    const newHeight = messagesEl.scrollHeight;
    messagesEl.scrollTop += newHeight - prevHeight;
    refreshOldestFromDom();
  };

  const updateMessageStatus = (ids) => {
    if (!Array.isArray(ids)) return;
    ids.forEach((id) => {
      const bubble = messagesEl.querySelector(`[data-message-id="${id}"]`);
      if (!bubble) return;
      bubble.dataset.messageStatus = 'read';
      const status = bubble.querySelector('.message-status');
      if (status) {
        status.textContent = 'Visto';
        status.classList.add('is-read');
      }
    });
  };

  const pendingRead = new Set();
  let readDebounce = null;
  const queueRead = (id) => {
    if (!readUrl || !id) return;
    pendingRead.add(Number(id));
    if (readDebounce) return;
    readDebounce = setTimeout(flushReadQueue, 600);
  };

  const flushReadQueue = async () => {
    if (!pendingRead.size) {
      readDebounce = null;
      return;
    }
    const payload = Array.from(pendingRead);
    pendingRead.clear();
    readDebounce = null;
    try {
      await fetch(readUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message_ids: payload }),
        credentials: 'same-origin',
      });
    } catch (error) {
      console.error('No se pudo confirmar lectura', error);
    }
  };

  const resetTypingText = () => {
    typingActive = false;
    if (typingText) {
      typingText.textContent = presenceState;
    }
  };

  const handleTypingWhisper = (payload) => {
    if (!typingText || !payload || Number(payload.user_id) === meId) return;
    typingActive = true;
    typingText.textContent = `${payload.name || otherName} está escribiendo…`;
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(resetTypingText, 2000);
  };

  const sendTypingWhisper = (channel) => {
    if (!channel) return;
    if (typingThrottle) return;
    typingThrottle = setTimeout(() => {
      typingThrottle = null;
    }, 1200);
    channel.whisper('typing', { user_id: meId, name: meName });
  };

  const toggleEmojiPicker = () => {
    if (!emojiPicker) return;
    emojiPicker.hidden = !emojiPicker.hidden;
  };

  const updateAttachmentChip = () => {
    if (!attachmentChip || !attachmentName) return;
    const file = recordedFile || attachmentInput?.files?.[0];
    if (!file) {
      attachmentChip.hidden = true;
      attachmentName.textContent = '';
      return;
    }
    attachmentName.textContent = file.name;
    attachmentChip.hidden = false;
  };

  const stopRecording = () => {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
      mediaRecorder.stop();
    }
    if (recordingIndicator) {
      recordingIndicator.hidden = true;
    }
    if (recordBtn) {
      recordBtn.classList.remove('is-recording');
    }
  };

  const clearAttachment = () => {
    if (attachmentInput) {
      attachmentInput.value = '';
    }
    recordedFile = null;
    stopRecording();
    updateAttachmentChip();
  };

  const startRecording = async () => {
    if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) {
      showError('Tu navegador no permite grabar audio.');
      return;
    }
    try {
      mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });
      mediaRecorder = new MediaRecorder(mediaStream);
      recordedChunks = [];
      mediaRecorder.addEventListener('dataavailable', (event) => {
        if (event.data?.size) recordedChunks.push(event.data);
      });
      mediaRecorder.addEventListener('stop', () => {
        mediaStream?.getTracks().forEach((track) => track.stop());
        mediaStream = null;
        if (recordedChunks.length) {
          const mimeType = mediaRecorder.mimeType || 'audio/webm';
          const blob = new Blob(recordedChunks, { type: mimeType });
          recordedFile = new File([blob], `nota-${Date.now()}.webm`, { type: mimeType });
          updateAttachmentChip();
        }
      });
      if (recordingIndicator) recordingIndicator.hidden = false;
      if (recordBtn) recordBtn.classList.add('is-recording');
      mediaRecorder.start();
    } catch (error) {
      console.error(error);
      showError('No se pudo acceder al micrófono.');
      stopRecording();
    }
  };

  const toggleRecording = () => {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
      stopRecording();
    } else {
      recordedFile = null;
      updateAttachmentChip();
      startRecording();
    }
  };

  attachmentInput?.addEventListener('change', () => {
    recordedFile = null;
    updateAttachmentChip();
  });

  attachmentRemove?.addEventListener('click', () => {
    clearAttachment();
  });

  emojiToggle?.addEventListener('click', (e) => {
    e.preventDefault();
    toggleEmojiPicker();
  });

  emojiPicker?.addEventListener('click', (event) => {
    const target = event.target.closest('.emoji-option');
    if (!target || !input) return;
    event.preventDefault();
    const emoji = target.dataset.emoji || target.textContent;
    const start = input.selectionStart ?? input.value.length;
    const end = input.selectionEnd ?? input.value.length;
    input.setRangeText(emoji, start, end, 'end');
    autoResize();
  });

  quickReplyButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      if (!input) return;
      const text = btn.dataset.quickReply || btn.textContent;
      input.value = text;
      input.focus();
      autoResize();
      if (window.chatChannel) {
        sendTypingWhisper(window.chatChannel);
      }
    });
  });

  if (attachmentInput && attachmentBtn) {
    attachmentBtn.addEventListener('click', (e) => {
      e.preventDefault();
      attachmentInput.click();
    });
  }

  if (recordBtn) {
    if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) {
      recordBtn.style.display = 'none';
    } else {
      recordBtn.addEventListener('click', (e) => {
        e.preventDefault();
        toggleRecording();
      });
    }
  }

  input?.addEventListener('input', () => {
    autoResize();
    if (window.chatChannel) {
      sendTypingWhisper(window.chatChannel);
    }
  });

  const hasAttachmentSelected = () => {
    return !!recordedFile || (attachmentInput?.files?.length ?? 0) > 0;
  };

  const triggerBrowserNotification = (msg) => {
    if (!notificationsAllowed || notificationsMuted || typeof Notification === 'undefined') return;
    if (document.visibilityState === 'visible') return;

    const senderName = msg.sender?.name || otherName;
    let body = msg.body || '';
    const attachmentType = msg.attachment_type || '';
    if (!body && msg.attachment_name) {
      body = `Adjunto: ${msg.attachment_name}`;
    } else if (!body && attachmentType.startsWith('audio/')) {
      body = 'Te envió una nota de voz';
    } else if (!body) {
      body = 'Te envió un mensaje';
    }

    const options = {
      body,
      tag: `conversation-${conversationId}`,
      icon: '/favicon.ico',
    };

    if (msg.attachment_url && attachmentType.startsWith('image/')) {
      options.image = msg.attachment_url;
    }

    try {
      const notification = new Notification(senderName, options);
      notification.onclick = () => window.focus();
    } catch (error) {
      console.error('No se pudo mostrar la notificación', error);
    }
  };

  const updateNotifyBtn = () => {
    if (!notifyBtn) return;
    if (typeof Notification === 'undefined') {
      notifyBtn.hidden = true;
      return;
    }
    const permission = Notification.permission;
    const enabled = notificationsAllowed && !notificationsMuted && permission === 'granted';
    notifyBtn.textContent = enabled ? '🔕' : '🔔';
    notifyBtn.title = enabled ? 'Silenciar notificaciones' : 'Activar notificaciones';
  };

  notifyBtn?.addEventListener('click', async (e) => {
    e.preventDefault();
    if (typeof Notification === 'undefined') return;

    if (Notification.permission === 'default') {
      try {
        const permission = await Notification.requestPermission();
        notificationsAllowed = permission === 'granted';
        notificationsMuted = false;
      } catch (error) {
        console.error(error);
      }
    } else if (Notification.permission === 'granted') {
      notificationsAllowed = true;
      notificationsMuted = !notificationsMuted;
    } else {
      showError('Las notificaciones están bloqueadas en tu navegador.');
    }
    updateNotifyBtn();
  });

  updateNotifyBtn();

  const fetchOlderMessages = async () => {
    if (!messagesUrl || !hasMore || !oldestId) return;
    updateLoadMoreVisibility(true);
    try {
      const url = new URL(messagesUrl, window.location.origin);
      url.searchParams.set('before', String(oldestId));
      url.searchParams.set('limit', '30');
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error('Error al cargar mensajes');
      const data = await res.json();
      prependMessages(data.messages || []);
      hasMore = Boolean(data.has_more);
      oldestId = Number(data.next_before || oldestId);
    } catch (error) {
      console.error(error);
      showError('No se pudieron cargar mensajes anteriores.');
    } finally {
      refreshOldestFromDom();
      updateLoadMoreVisibility(false);
    }
  };

  loadMoreBtn?.addEventListener('click', () => {
    fetchOlderMessages();
  });

  const clearSearchResults = () => {
    if (searchList) searchList.innerHTML = '';
    if (searchSummary) searchSummary.textContent = 'Resultados';
    if (searchResults) searchResults.hidden = true;
  };

  const renderSearchResults = (items = [], term = '') => {
    if (!searchResults || !searchList) return;
    searchList.innerHTML = '';
    if (!items.length) {
      const empty = document.createElement('li');
      empty.className = 'chat-search-results__empty';
      empty.textContent = 'Sin coincidencias';
      searchList.appendChild(empty);
    } else {
      items.forEach((item) => {
        const li = document.createElement('li');
        li.className = 'chat-search-results__item';
        li.dataset.messageId = item.id;

        const title = document.createElement('div');
        title.className = 'chat-search-results__snippet';
        title.textContent = item.body || item.attachment_name || 'Archivo adjunto';

        const time = document.createElement('small');
        time.className = 'chat-search-results__time';
        time.textContent = formatTime(item.created_at);

        li.append(title, time);
        searchList.appendChild(li);
      });
    }
    if (searchSummary) {
      const count = items.length;
      searchSummary.textContent = count
        ? `${count} resultado${count === 1 ? '' : 's'} para "${term}"`
        : `Sin resultados para "${term}"`;
    }
    searchResults.hidden = false;
  };

  const ensureMessageVisible = async (messageId) => {
    let bubble = messagesEl.querySelector(`[data-message-id="${messageId}"]`);
    let guard = 0;
    while (!bubble && hasMore && guard < 10) {
      guard += 1;
      await fetchOlderMessages();
      bubble = messagesEl.querySelector(`[data-message-id="${messageId}"]`);
    }
    return bubble;
  };

  const focusMessage = async (messageId) => {
    if (!messageId) return;
    const bubble = await ensureMessageVisible(messageId);
    if (!bubble) {
      showError('Carga los mensajes anteriores para ver este resultado.');
      return;
    }
    bubble.scrollIntoView({ behavior: 'smooth', block: 'center' });
    highlightBubble(bubble);
  };

  searchList?.addEventListener('click', (event) => {
    const item = event.target.closest('.chat-search-results__item');
    if (!item) return;
    const messageId = Number(item.dataset.messageId);
    focusMessage(messageId);
  });

  searchCloseBtn?.addEventListener('click', () => {
    clearSearchResults();
  });

  searchClearBtn?.addEventListener('click', () => {
    if (searchInput) searchInput.value = '';
    clearSearchResults();
  });

  searchForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!messagesUrl || !searchInput) return;
    const term = searchInput.value.trim();
    if (!term) {
      clearSearchResults();
      return;
    }
    searchForm.classList.add('is-loading');
    try {
      const url = new URL(messagesUrl, window.location.origin);
      url.searchParams.set('q', term);
      url.searchParams.set('limit', '50');
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error('Error al buscar mensajes');
      const data = await res.json();
      renderSearchResults(data.messages || [], term);
    } catch (error) {
      console.error(error);
      showError('No se pudo buscar en la conversación.');
    } finally {
      searchForm.classList.remove('is-loading');
    }
  });

  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const bodyValue = input.value.trim();
      const hasAttachment = hasAttachmentSelected();
      if (!bodyValue && !hasAttachment) {
        showError('Escribe algo o adjunta un archivo.');
        return;
      }

      setSending(true);
      if (errBox) errBox.style.display = 'none';

      const formData = new FormData();
      if (bodyValue) formData.append('body', bodyValue);
      const fileToSend = recordedFile || attachmentInput?.files?.[0];
      if (fileToSend) formData.append('attachment', fileToSend);

      try {
        const res = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          },
          body: formData,
          credentials: 'same-origin',
        });

        if (!res.ok) {
          if (res.status === 419) showError('Sesión expirada. Recarga la página.');
          else if (res.status === 403) showError('No autorizado para esta conversación.');
          else if (res.status === 422) {
            const json = await res.json().catch(() => ({}));
            showError(json.message || 'El mensaje no es válido.');
          } else showError('Error al enviar (' + res.status + ').');
          return;
        }

        const msg = await res.json();
        msg.sender = msg.sender || { id: meId, name: meName };
        msg.created_at = msg.created_at || new Date().toISOString();
        appendMessage(msg);
        input.value = '';
        autoResize();
        clearAttachment();
      } catch (err) {
        console.error(err);
        showError('No se pudo conectar con el servidor.');
      } finally {
        setSending(false);
        input?.focus();
      }
    });
  }

  const updatePresenceFromUsers = (users = []) => {
    const otherOnline = users.some((user) => Number(user.id) === otherId);
    setPresenceState(
      otherOnline ? `${otherName} está en línea` : `${otherName} estuvo en línea recientemente`,
      otherOnline
    );
  };

  if (window.Echo && conversationId) {
    window.chatChannel = window.Echo.private(`conversation.${conversationId}`)
      .listen('MessageSent', (event) => {
        if (event && event.message) {
          appendMessage(event.message);
        }
      })
      .listen('MessagesRead', (event) => {
        if (!event || Number(event.reader_id) === meId) return;
        updateMessageStatus(event.message_ids || []);
      });

    window.chatChannel.listenForWhisper('typing', handleTypingWhisper);

    if (window.Echo.join) {
      let members = [];
      window.chatPresence = window.Echo.join(`presence.conversation.${conversationId}`)
        .here((users) => {
          members = users || [];
          updatePresenceFromUsers(members);
        })
        .joining((user) => {
          members = [...members.filter((member) => Number(member.id) !== Number(user.id)), user];
          updatePresenceFromUsers(members);
        })
        .leaving((user) => {
          members = members.filter((member) => Number(member.id) !== Number(user.id));
          updatePresenceFromUsers(members);
        });
    }
  }
})();
</script>
