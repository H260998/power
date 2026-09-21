-- Run once in the SQL Editor of the Supabase project used by POWER.
-- Laravel connects directly to PostgreSQL; these tables are not part of
-- Supabase's public Data API. Migrations create the tables afterwards.
create schema if not exists laravel;
revoke all on schema laravel from public, anon, authenticated;
