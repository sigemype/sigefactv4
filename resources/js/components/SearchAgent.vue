<template>
    <div class="form-group">
        <label class="control-label">
            Agente
        </label>
        <el-select v-model="agent_id"
                    :loading="loading_search"
                    :remote-method="searchRemoteAgents"
                    filterable
                    placeholder="Escriba el nombre o código del agente"
                    remote
                    clearable
                    @change="changeAgent">

            <el-option v-for="option in agents"
                        :key="option.id"
                        :label="option.search_description"
                        :value="option.id"></el-option>

        </el-select>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                loading_search: false,
                resource: 'agents',
                agent_id: null,
                agents: [],
                all_agents: [],
            }
        },
        created() 
        {
            this.initAgents()
        },
        methods: {
            initAgents()
            {
                this.httpAgents()
                    .then(response => {
                        this.all_agents = response.data
                        this.filterAgents()
                    })
            },
            async searchRemoteAgents(input) 
            {
                if (input.length > 0) 
                {
                    this.loading_search = true
                    
                    const parameters = {
                        input: input
                    }

                    await this.httpAgents(parameters)
                                .then(response => {
                                    this.agents = response.data
                                    this.loading_search = false
                                })
                } 
                else 
                {
                    this.filterAgents()
                }

            },
            async httpAgents(params = null)
            {
                return await this.$http.get(`/${this.resource}/search`, {params})
            },
            filterAgents()
            {
                this.agents = this.all_agents
            },
            changeAgent() 
            {
                this.$emit('changeAgent', this.agent_id)
            },
        }
    }
</script>