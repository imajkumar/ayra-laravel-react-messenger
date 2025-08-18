import React, { useState, useEffect, useRef } from 'react';
import { useInertia } from '@inertiajs/react';
import { Send, Paperclip, Smile, MoreVertical, Search, Phone, Video, Info } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Badge } from '@/components/ui/badge';
import { useChat } from '@/hooks/useChat';
import { useSocket } from '@/hooks/useSocket';
import MessageBubble from './MessageBubble';
import FileUpload from './FileUpload';
import EmojiPicker from './EmojiPicker';
import TypingIndicator from './TypingIndicator';

interface ChatInterfaceProps {
  conversation: any;
  messages: any[];
  participants: any[];
  user: any;
}

const ChatInterface: React.FC<ChatInterfaceProps> = ({
  conversation,
  messages,
  participants,
  user
}) => {
  const [message, setMessage] = useState('');
  const [isTyping, setIsTyping] = useState(false);
  const [showEmojiPicker, setShowEmojiPicker] = useState(false);
  const [showFileUpload, setShowFileUpload] = useState(false);
  const [replyTo, setReplyTo] = useState<any>(null);
  
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);
  
  const { sendMessage, sendTyping, stopTyping } = useChat();
  const { socket, isConnected } = useSocket();
  
  const { post } = useInertia();

  // Auto-scroll to bottom when new messages arrive
  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  // Handle typing indicators
  useEffect(() => {
    let typingTimeout: NodeJS.Timeout;
    
    if (isTyping) {
      sendTyping(conversation.id);
      typingTimeout = setTimeout(() => {
        setIsTyping(false);
        stopTyping(conversation.id);
      }, 3000);
    }

    return () => {
      if (typingTimeout) {
        clearTimeout(typingTimeout);
      }
    };
  }, [isTyping, conversation.id, sendTyping, stopTyping]);

  // Listen for incoming messages
  useEffect(() => {
    if (!socket) return;

    socket.on('message:received', (data: any) => {
      if (data.conversation_id === conversation.id) {
        // Handle new message
        post(route('chat.message.store'), {
          conversation_id: conversation.id,
          content: data.content,
          type: data.type,
        });
      }
    });

    socket.on('typing:started', (data: any) => {
      if (data.conversation_id === conversation.id && data.user_id !== user.id) {
        // Show typing indicator for other users
      }
    });

    socket.on('typing:stopped', (data: any) => {
      if (data.conversation_id === conversation.id && data.user_id !== user.id) {
        // Hide typing indicator for other users
      }
    });

    return () => {
      socket.off('message:received');
      socket.off('typing:started');
      socket.off('typing:stopped');
    };
  }, [socket, conversation.id, user.id, post]);

  const handleSendMessage = async () => {
    if (!message.trim()) return;

    try {
      await sendMessage({
        conversation_id: conversation.id,
        content: message,
        type: 'text',
        parent_id: replyTo?.id,
      });

      setMessage('');
      setReplyTo(null);
      setIsTyping(false);
      
      // Focus back to input
      inputRef.current?.focus();
    } catch (error) {
      console.error('Failed to send message:', error);
    }
  };

  const handleKeyPress = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSendMessage();
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setMessage(e.target.value);
    
    if (!isTyping && e.target.value.length > 0) {
      setIsTyping(true);
    } else if (isTyping && e.target.value.length === 0) {
      setIsTyping(false);
    }
  };

  const handleEmojiSelect = (emoji: string) => {
    setMessage(prev => prev + emoji);
    setShowEmojiPicker(false);
    inputRef.current?.focus();
  };

  const handleFileUpload = (files: File[]) => {
    // Handle file upload logic
    console.log('Files to upload:', files);
    setShowFileUpload(false);
  };

  const getConversationTitle = () => {
    if (conversation.type === 'private') {
      const otherParticipant = participants.find(p => p.id !== user.id);
      return otherParticipant?.name || otherParticipant?.email || 'Unknown User';
    }
    return conversation.name || 'Group Chat';
  };

  const getConversationAvatar = () => {
    if (conversation.type === 'private') {
      const otherParticipant = participants.find(p => p.id !== user.id);
      return otherParticipant?.avatar || otherParticipant?.profile_photo_url;
    }
    return conversation.avatar;
  };

  return (
    <div className="flex flex-col h-full bg-gray-50 dark:bg-gray-900">
      {/* Header */}
      <div className="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div className="flex items-center space-x-3">
          <Avatar className="h-10 w-10">
            <AvatarImage src={getConversationAvatar()} />
            <AvatarFallback>
              {getConversationTitle().charAt(0).toUpperCase()}
            </AvatarFallback>
          </Avatar>
          
          <div>
            <h3 className="font-semibold text-gray-900 dark:text-white">
              {getConversationTitle()}
            </h3>
            <p className="text-sm text-gray-500 dark:text-gray-400">
              {isConnected ? 'Online' : 'Offline'}
            </p>
          </div>
        </div>

        <div className="flex items-center space-x-2">
          <Button variant="ghost" size="sm">
            <Phone className="h-4 w-4" />
          </Button>
          <Button variant="ghost" size="sm">
            <Video className="h-4 w-4" />
          </Button>
          <Button variant="ghost" size="sm">
            <Search className="h-4 w-4" />
          </Button>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="sm">
                <MoreVertical className="h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuItem>
                <Info className="h-4 w-4 mr-2" />
                Conversation Info
              </DropdownMenuItem>
              <DropdownMenuItem>
                <Search className="h-4 w-4 mr-2" />
                Search Messages
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>

      {/* Messages Area */}
      <ScrollArea className="flex-1 p-4">
        <div className="space-y-4">
          {messages.map((msg) => (
            <MessageBubble
              key={msg.id}
              message={msg}
              isOwn={msg.user_id === user.id}
              onReply={(message) => setReplyTo(message)}
            />
          ))}
          
          {/* Typing Indicator */}
          {isTyping && <TypingIndicator />}
          
          <div ref={messagesEndRef} />
        </div>
      </ScrollArea>

      {/* Reply Preview */}
      {replyTo && (
        <div className="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border-t border-blue-200 dark:border-blue-800">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <div className="w-1 h-8 bg-blue-500 rounded-full" />
              <div className="text-sm">
                <p className="font-medium text-blue-900 dark:text-blue-100">
                  Replying to {replyTo.user?.name || 'Unknown'}
                </p>
                <p className="text-blue-700 dark:text-blue-300 truncate max-w-xs">
                  {replyTo.content}
                </p>
              </div>
            </div>
            <Button
              variant="ghost"
              size="sm"
              onClick={() => setReplyTo(null)}
              className="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200"
            >
              ×
            </Button>
          </div>
        </div>
      )}

      {/* Input Area */}
      <div className="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div className="flex items-center space-x-2">
          <Button
            variant="ghost"
            size="sm"
            onClick={() => setShowFileUpload(!showFileUpload)}
            className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
          >
            <Paperclip className="h-5 w-5" />
          </Button>
          
          <div className="flex-1 relative">
            <Input
              ref={inputRef}
              value={message}
              onChange={handleInputChange}
              onKeyPress={handleKeyPress}
              placeholder="Type a message..."
              className="pr-20"
            />
            
            {/* Emoji Picker */}
            {showEmojiPicker && (
              <div className="absolute bottom-full right-0 mb-2">
                <EmojiPicker onEmojiSelect={handleEmojiSelect} />
              </div>
            )}
          </div>
          
          <Button
            variant="ghost"
            size="sm"
            onClick={() => setShowEmojiPicker(!showEmojiPicker)}
            className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
          >
            <Smile className="h-5 w-5" />
          </Button>
          
          <Button
            onClick={handleSendMessage}
            disabled={!message.trim()}
            className="bg-blue-600 hover:bg-blue-700 text-white"
          >
            <Send className="h-4 w-4" />
          </Button>
        </div>
      </div>

      {/* File Upload Modal */}
      {showFileUpload && (
        <FileUpload
          onUpload={handleFileUpload}
          onClose={() => setShowFileUpload(false)}
          conversationId={conversation.id}
        />
      )}
    </div>
  );
};

export default ChatInterface;
