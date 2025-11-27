@extends('layouts.app')

@section('title', 'AI Assistant')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">AI Assistant</h1>
        <p class="mt-2 text-gray-600">Get help with your questions about payments, scholarships, and more</p>
    </div>

    <!-- Chat Interface -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-96 flex flex-col">
        <!-- Chat Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">AIJA Assistant</h3>
                    <p class="text-sm text-gray-600">Online • Ready to help</p>
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="chat-messages" class="flex-1 p-6 overflow-y-auto space-y-4">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="bg-gray-100 rounded-lg p-4 max-w-xs lg:max-w-md">
                    <p class="text-gray-800">Hi! I'm your AI assistant. I can help you with:</p>
                    <ul class="mt-2 text-sm text-gray-600 space-y-1">
                        <li>• Payment questions</li>
                        <li>• Scholarship information</li>
                        <li>• Bill management</li>
                        <li>• Account settings</li>
                    </ul>
                    <p class="mt-2 text-sm text-gray-600">What would you like to know?</p>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="px-6 py-4 border-t border-gray-200 space-y-3">
            <div class="flex items-center space-x-3">
                <label for="language-select" class="text-sm text-gray-600">Language:</label>
                <select id="language-select" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="auto">Auto</option>
                    <option value="id" selected>Bahasa Indonesia</option>
                    <option value="en">English</option>
                    <option value="both">Both (ID + EN)</option>
                </select>
            </div>
            <div class="flex space-x-3">
                <input type="text" id="message-input" placeholder="Type your message..."
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button onclick="sendMessage()" id="send-button"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
            <div class="text-xs text-gray-500 text-center">
                AI responses will follow the selected language preference
            </div>
        </div>
    </div>

    <!-- Quick Questions -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Questions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <button onclick="askQuestion('How do I pay my bills?')" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                <h4 class="font-medium text-gray-900">How do I pay my bills?</h4>
                <p class="text-sm text-gray-600 mt-1">Learn about payment methods</p>
            </button>

            <button onclick="askQuestion('What scholarships am I eligible for?')" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                <h4 class="font-medium text-gray-900">Scholarship eligibility</h4>
                <p class="text-sm text-gray-600 mt-1">Check your matches</p>
            </button>

            <button onclick="askQuestion('How does the AI anomaly detection work?')" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                <h4 class="font-medium text-gray-900">AI Security features</h4>
                <p class="text-sm text-gray-600 mt-1">Learn about fraud protection</p>
            </button>

            <button onclick="askQuestion('How do I update my profile?')" class="p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                <h4 class="font-medium text-gray-900">Profile management</h4>
                <p class="text-sm text-gray-600 mt-1">Update your information</p>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let conversationHistory = [];

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');

    // Enter key to send
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Auto-resize input (optional)
    input.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});

function sendMessage() {
    const input = document.getElementById('message-input');
    const message = input.value.trim();

    if (!message) return;

    // Add user message
    addMessage(message, 'user');
    conversationHistory.push({ role: 'user', content: message });

    // Clear input
    input.value = '';
    input.style.height = 'auto';

    // Show typing indicator
    showTypingIndicator();

    // Send to API
    fetch('/api/chatbot/message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            message: message,
            history: conversationHistory,
            language: document.getElementById('language-select').value
        })
    })
    .then(response => response.json())
    .then(data => {
        hideTypingIndicator();

        if (data.success) {
            addMessage(data.response, 'bot');
            conversationHistory.push({ role: 'assistant', content: data.response });
        } else {
            addMessage('Sorry, I encountered an error. Please try again.', 'bot');
        }
    })
    .catch(error => {
        hideTypingIndicator();
        console.error('Error sending message:', error);
        addMessage('Sorry, I\'m having trouble connecting. Please try again later.', 'bot');
    });
}

function askQuestion(question) {
    document.getElementById('message-input').value = question;
    sendMessage();
}

function addMessage(content, sender) {
    const messagesContainer = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex items-start space-x-3 ${sender === 'user' ? 'justify-end' : ''}`;

    if (sender === 'user') {
        messageDiv.innerHTML = `
            <div class="bg-blue-600 text-white rounded-lg p-4 max-w-xs lg:max-w-md">
                <p>${content}</p>
            </div>
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-medium text-gray-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
        `;
    } else {
        messageDiv.innerHTML = `
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="bg-gray-100 rounded-lg p-4 max-w-xs lg:max-w-md">
                <p class="text-gray-800">${content}</p>
            </div>
        `;
    }

    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function showTypingIndicator() {
    const messagesContainer = document.getElementById('chat-messages');
    const indicator = document.createElement('div');
    indicator.id = 'typing-indicator';
    indicator.className = 'flex items-start space-x-3';
    indicator.innerHTML = `
        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div class="bg-gray-100 rounded-lg p-4">
            <div class="flex space-x-1">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    `;

    messagesContainer.appendChild(indicator);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function hideTypingIndicator() {
    const indicator = document.getElementById('typing-indicator');
    if (indicator) {
        indicator.remove();
    }
}

function getToken() {
    return localStorage.getItem('api_token') || '';
}
</script>
@endpush
@endsection
