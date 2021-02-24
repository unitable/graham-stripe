<?php

namespace Unitable\GrahamStripe\Engine;

use Unitable\Graham\Engine\Engine;

final class StripeEngine extends Engine {

    use Concerns\ManagesSubscriptions;
    use Concerns\ManagesInvoices;

}
