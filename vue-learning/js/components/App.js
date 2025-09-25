
import Assignments from './Assignments.js'
import Panel from './Panel.js'

export default {

    components: {Assignments, Panel},

    template: 
    `
        <div class="grid gap-6">
            
            <assignments></assignments>

            <panel>
                <template v-slot:heading>
                    This is my heading slot
                </template>

                This is my default
                <template v-slot:footer>
                    This is my footer slot
                </template>

            </panel>
        </div>
    `

}