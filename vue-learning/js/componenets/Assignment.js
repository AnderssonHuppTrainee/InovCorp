export default {
    template: `
       <li>
            <label>
                <input type="checkbox" v-model="assignment.complete">
                {{assignment.name}}
            </label>
        </li>
        `,

        props: {
            assignment: Object
        }
}