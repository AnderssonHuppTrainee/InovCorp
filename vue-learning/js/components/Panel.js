export default {

    template: `
        <div class="bg-gray-700 p-4 border border-gray-600 rounded-lg">
        
            <h2 v-if="$slots.heading" class="font-bold mb-2">
                <slot name="heading"></slot>
            </h2>

            <slot></slot>

            <footer v-if="$slots.footer" class="border-gray-600 border-t mt-4 pt-4 text-xs">
                <slot name="footer"></slot>
            </footer>
        
        </div>
    
    `,
  
}