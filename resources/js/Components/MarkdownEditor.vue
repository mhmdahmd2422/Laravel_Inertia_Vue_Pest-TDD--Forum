<script setup>

import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import {watch} from "vue";
import {Markdown} from "tiptap-markdown";
import Link from '@tiptap/extension-link'

const props = defineProps({
   modelValue: '',
});

const emit = defineEmits(['update:modelValue']);

const promptUserForHref = () => {
  if(editor.value?.isActive('link')){
    return editor.value?.chain().focus().unsetLink().run();
  }

  const href = prompt('Where you want to link to?');

  if( !href ){
    return editor.value?.chain().focus().run();
  }

  return editor.value?.chain().focus().setLink({ href }).run();
}

const editor = useEditor({
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [2, 3, 4],
            },
            code: false,
            codeBlock: false,
        }),
        Markdown,
        Link,
    ],
    editorProps: {
        attributes: {
            class: 'min-h-[512px] prose prose-sm max-w-none py-1.5 px-3',
        },
    },
    onUpdate: () => emit('update:modelValue', editor.value?.storage.markdown.getMarkdown()),
});

watch(() => props.modelValue, (value) => {
    if(value === editor.value?.storage.markdown.getMarkdown()){
        return;
    }

   editor.value?.commands.setContent(value);
}, {immediate: true});

</script>

<template>
    <div v-if="editor" class="bg-white rounded-md border-0 shadow-sm">
        <menu class="flex divide-x border-b">
            <li>
                <button @click="() => editor.chain().focus().toggleBold().run()"
                        type="button"
                        class="px-3 py-2 rounded-tl ring-1 ring-gray-100"
                        :class="[
                            editor.isActive('bold')?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                        title="Bold">
                    <v-icon name="ri-bold"></v-icon>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleItalic().run()"
                        type="button"
                        class="px-3 py-2 ring-1 ring-gray-100"
                        :class="[
                            editor.isActive('italic')?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                        title="Italic">
                    <v-icon name="ri-italic"></v-icon>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleStrike().run()"
                        type="button"
                        class="px-3 py-2 ring-1 ring-gray-100"
                        :class="[
                            editor.isActive('strike')?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                        title="Strikethrough">
                    <v-icon name="ri-strikethrough"></v-icon>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleBlockquote().run()"
                        type="button"
                        class="px-3 py-2 ring-1 ring-gray-100"
                        :class="[
                            editor.isActive('blockquote')?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                        title="Blockquote">
                    <v-icon name="ri-double-quotes-l"></v-icon>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleBulletList().run()"
                        type="button"
                        class="px-3 py-2 ring-1 ring-gray-100"
                        :class="[
                            editor.isActive('bulletList')?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                        title="Bullet List">
                    <v-icon name="ri-list-unordered"></v-icon>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleOrderedList().run()"
                        type="button"
                        class="px-3 py-2 ring-1 ring-gray-100"
                        :class="[
                            editor.isActive('orderedList')?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                        title="Numeric List">
                    <v-icon name="ri-list-ordered"></v-icon>
                </button>
            </li>
          <li>
            <button @click="promptUserForHref"
                    type="button"
                    class="px-3 py-2 ring-1 ring-gray-100"
                    :class="[
                            editor.isActive('link')?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                    title="Link">
              <v-icon name="ri-link"></v-icon>
            </button>
          </li>
            <li>
                <button @click="() => editor.chain().focus().toggleHeading({ level: 2 }).run()"
                        type="button"
                        class="px-3 py-2 ring-1 ring-gray-100"
                        :class="[
                            editor.isActive('heading', { level: 2 })?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                        title="Heading 1">
                    <v-icon name="ri-h-1"></v-icon>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleHeading({ level: 3 }).run()"
                        type="button"
                        class="px-3 py-2 ring-1 ring-gray-100"
                        :class="[
                            editor.isActive('heading', { level: 3 })?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                        title="Heading 2">
                    <v-icon name="ri-h-2"></v-icon>
                </button>
            </li>
            <li>
                <button @click="() => editor.chain().focus().toggleHeading({ level: 4 }).run()"
                        type="button"
                        class="px-3 py-2 ring-1 ring-gray-100"
                        :class="[
                            editor.isActive('heading', { level: 4 })?
                            'bg-midnight-50':
                            'hover:bg-gray-200',
                        ]"
                        title="Heading 3">
                    <v-icon name="ri-h-3"></v-icon>
                </button>
            </li>
        </menu>
        <EditorContent :editor="editor" />
    </div>
</template>

