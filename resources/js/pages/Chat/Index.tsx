import React from 'react';
import { Head } from '@inertiajs/react';
import ChatLayout from '@/Layouts/ChatLayout';
import ChatInterface from '@/components/ChatInterface';

interface Props {
  conversations: any[];
  unreadCounts: any[];
  user: any;
}

export default function ChatIndex({ conversations, unreadCounts, user }: Props) {
  return (
    <ChatLayout>
      <Head title="Chat" />
      
      <div className="h-screen flex">
        {/* Sidebar */}
        <div className="w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
          <div className="p-4">
            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
              Conversations
            </h2>
          </div>
          
          <div className="flex-1 overflow-y-auto">
            {conversations.map((conversation) => (
              <div
                key={conversation.id}
                className="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-600"
              >
                <div className="flex items-center space-x-3">
                  <div className="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                    <span className="text-sm font-medium text-gray-600">
                      {conversation.name?.charAt(0) || 'C'}
                    </span>
                  </div>
                  
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-900 dark:text-white truncate">
                      {conversation.name || 'Untitled'}
                    </p>
                    <p className="text-sm text-gray-500 dark:text-gray-400 truncate">
                      {conversation.last_message_at ? 'Last message' : 'No messages yet'}
                    </p>
                  </div>
                  
                  {unreadCounts[conversation.id] > 0 && (
                    <span className="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                      {unreadCounts[conversation.id]}
                    </span>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
        
        {/* Main Chat Area */}
        <div className="flex-1 bg-gray-50 dark:bg-gray-900">
          <div className="h-full flex items-center justify-center">
            <div className="text-center">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Welcome to LaraChat
              </h3>
              <p className="text-gray-500 dark:text-gray-400">
                Select a conversation to start chatting
              </p>
            </div>
          </div>
        </div>
      </div>
    </ChatLayout>
  );
}