<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Free trial length for new owners (days)
    |--------------------------------------------------------------------------
    |
    | Matches the "Starter" free plan. New house owners start on a trial
    | before they must pick a paid billing period (e.g. quarterly or yearly).
    |
    */
    'trial_days' => (int) env('SUBSCRIPTION_TRIAL_DAYS', 15),

    /*
    | Deprecated: use trial_days. Kept for older env files.
    */
    'trial_months' => (int) env('SUBSCRIPTION_TRIAL_MONTHS', 0),

];
