@extends('layouts.app')

@section('content')
@php
  $yo   = auth()->id();
  $otro = $yo === $conversation->agent_id ? $conversation->client : $conversation->agent;
@endphp

@include('chat.partials.styles')

{{-- Flatpickr para calendario de reservas en el chat --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

@include('chat.partials.frame', [
  'conversation' => $conversation,
  'messages' => $messages,
  'property' => $property ?? null,
  'nextVisit' => $nextVisit ?? null,
  'activeReservation' => $activeReservation ?? null,
  'yo' => $yo,
  'otro' => $otro,
  'hasMoreMessages' => $hasMoreMessages ?? false,
  'oldestMessageId' => $oldestMessageId ?? null,
])

@include('chat.partials.script', ['conversation' => $conversation])
@endsection
