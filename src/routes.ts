import Home from './routes/Home.svelte';
import Error from './routes/Error.svelte';
import BankAccounts from './routes/BankAccounts.svelte';
import Analytics from './routes/Analytics.svelte';
import Triage from './routes/Triage.svelte';
import TagRules from './routes/TagRules.svelte';
import Tags from './routes/Tags.svelte';
import TagEdit from './routes/TagEdit.svelte';
import Import from './routes/Import.svelte';
import Operations from './routes/Operations.svelte';

const routes = {
    '/': Home,
    '/operations': Operations,
    '/bank-accounts': BankAccounts,
    '/analytics': Analytics,
    '/triage': Triage,
    '/tag-rules': TagRules,
    '/tags': Tags,
    '/tag/edit/:id': TagEdit,
    '/import': Import,
    '*': Error,
};

export default routes;