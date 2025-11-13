@extends('layouts.app')

@section('content')
@php
  $yo = auth()->id();
  $otro = $yo === $conversation->agent_id ? $conversation->client : $conversation->agent;
@endphp

@include('chat.partials.styles')

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
