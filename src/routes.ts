import Home from './routes/Home.svelte';
import Error from './routes/Error.svelte';
import BankAccounts from './routes/bank_accounts/BankAccounts.svelte';
import NewBankAccount from './routes/bank_accounts/NewBankAccount.svelte';
import Analytics from './routes/Analytics.svelte';
import Triage from './routes/Triage.svelte';
import TagRules from './routes/tag_rules/TagRules.svelte';
import EditTagRule from './routes/tag_rules/EditTagRule.svelte';
import Tags from './routes/tags/Tags.svelte';
import TagEdit from './routes/tags/TagEdit.svelte';
import Import from './routes/Import.svelte';
import Operations from './routes/Operations.svelte';

const routes = {
    '/': Home,
    '/operations': Operations,
    '/bank-accounts': BankAccounts,
    '/bank-accounts/new': NewBankAccount,
    '/analytics': Analytics,
    '/triage': Triage,
    '/tag-rules': TagRules,
    '/tag-rule/edit/:id': EditTagRule,
    '/tags': Tags,
    '/tag/edit/:id': TagEdit,
    '/import': Import,
    '*': Error,
};

export default routes;
