-- WARNING: This schema is for context only and is not meant to be run.
-- Table order and constraints may not be valid for execution.

CREATE TABLE public.agency_settings (
    id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
    payout_rate_per_token integer DEFAULT 8000,
    updated_at timestamp without time zone DEFAULT now(),
    CONSTRAINT agency_settings_pkey PRIMARY KEY (id)
);

CREATE TABLE public.audit_logs (
    id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
    user_id uuid,
    action character varying NOT NULL,
    description text,
    ip_address character varying,
    user_agent text,
    created_at timestamp without time zone DEFAULT now(),
    CONSTRAINT audit_logs_pkey PRIMARY KEY (id),
    CONSTRAINT audit_logs_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users (id)
);

CREATE TABLE public.deliverables (
    id uuid NOT NULL DEFAULT gen_random_uuid (),
    task_id uuid,
    staff_id uuid,
    file_url text NOT NULL,
    file_type text,
    message text,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    CONSTRAINT deliverables_pkey PRIMARY KEY (id),
    CONSTRAINT deliverables_task_id_fkey FOREIGN KEY (task_id) REFERENCES public.tasks (id),
    CONSTRAINT deliverables_staff_id_fkey FOREIGN KEY (staff_id) REFERENCES public.users (id)
);

CREATE TABLE public.invoices (
  id uuid NOT NULL DEFAULT gen_random_uuid(),
  user_id uuid NOT NULL,
  invoice_number character varying NOT NULL UNIQUE,
  amount numeric NOT NULL,
  status character varying NOT NULL DEFAULT 'unpaid'::character varying,
  description text NOT NULL,
  payment_method character varying,
  payment_link text,
  paid_at timestamp without time zone,
  due_date timestamp without time zone,
  created_at timestamp without time zone DEFAULT now(),
  updated_at timestamp without time zone DEFAULT now(),
  snap_token text,
  CONSTRAINT invoices_pkey PRIMARY KEY (id),
  CONSTRAINT invoices_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id)
);

CREATE TABLE public.notification_batches (
    id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
    title character varying NOT NULL,
    message text NOT NULL,
    type character varying NOT NULL,
    target_audience character varying NOT NULL,
    sender_id uuid,
    created_at timestamp without time zone DEFAULT now(),
    CONSTRAINT notification_batches_pkey PRIMARY KEY (id),
    CONSTRAINT notification_batches_sender_id_fkey FOREIGN KEY (sender_id) REFERENCES public.users (id)
);

CREATE TABLE public.notifications (
    id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
    user_id uuid,
    title character varying,
    message text,
    type character varying,
    reference_id uuid,
    is_read boolean DEFAULT false,
    created_at timestamp without time zone,
    batch_id bigint,
    CONSTRAINT notifications_pkey PRIMARY KEY (id),
    CONSTRAINT notifications_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users (id),
    CONSTRAINT notifications_batch_id_fkey FOREIGN KEY (batch_id) REFERENCES public.notification_batches (id)
);

CREATE TABLE public.password_reset_tokens (
    email character varying NOT NULL,
    token character varying NOT NULL,
    created_at timestamp without time zone,
    CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email)
);

CREATE TABLE public.services (
    id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
    name character varying NOT NULL,
    slug character varying NOT NULL UNIQUE,
    description text,
    toratix_cost integer NOT NULL DEFAULT 1,
    icon_url text,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone,
    updated_at timestamp without time zone DEFAULT now(),
    staff_commission integer DEFAULT 0,
    CONSTRAINT services_pkey PRIMARY KEY (id)
);

CREATE TABLE public.sessions (
    id character varying NOT NULL,
    user_id uuid,
    ip_address character varying,
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL,
    CONSTRAINT sessions_pkey PRIMARY KEY (id),
    CONSTRAINT sessions_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users (id)
);

CREATE TABLE public.staff_payouts (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  user_id uuid NOT NULL,
  amount_token integer NOT NULL,
  amount_currency numeric NOT NULL,
  status character varying DEFAULT 'pending'::character varying,
  proof_url text,
  created_at timestamp without time zone DEFAULT now(),
  updated_at timestamp without time zone DEFAULT now(),
  bank_name character varying,
  bank_account character varying,
  bank_holder character varying,
  admin_note text,
  CONSTRAINT staff_payouts_pkey PRIMARY KEY (id),
  CONSTRAINT staff_payouts_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id)
);

CREATE TABLE public.task_assignees (
    task_id uuid NOT NULL,
    staff_id uuid NOT NULL,
    assigned_at timestamp without time zone,
    CONSTRAINT task_assignees_pkey PRIMARY KEY (task_id, staff_id),
    CONSTRAINT task_assignees_task_id_fkey FOREIGN KEY (task_id) REFERENCES public.tasks (id),
    CONSTRAINT task_assignees_staff_id_fkey FOREIGN KEY (staff_id) REFERENCES public.users (id)
);

CREATE TABLE public.task_messages (
    id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
    task_id uuid,
    sender_id uuid,
    content text NOT NULL,
    is_read boolean DEFAULT false,
    attachment_url text,
    created_at timestamp without time zone,
    CONSTRAINT task_messages_pkey PRIMARY KEY (id),
    CONSTRAINT task_messages_task_id_fkey FOREIGN KEY (task_id) REFERENCES public.tasks (id),
    CONSTRAINT task_messages_sender_id_fkey FOREIGN KEY (sender_id) REFERENCES public.users (id)
);

CREATE TABLE public.tasks (
  id uuid NOT NULL DEFAULT gen_random_uuid(),
  user_id uuid NOT NULL,
  service_id bigint NOT NULL,
  title character varying NOT NULL,
  description text,
  brief_data jsonb,
  attachments jsonb,
  status USER-DEFINED DEFAULT 'queue'::task_status,
  deadline date,
  started_at timestamp without time zone,
  completed_at timestamp without time zone,
  toratix_locked integer NOT NULL,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  workspace_id uuid,
  assignee_id uuid,
  active_at timestamp without time zone,
  review_at timestamp without time zone,
  CONSTRAINT tasks_pkey PRIMARY KEY (id),
  CONSTRAINT tasks_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id),
  CONSTRAINT tasks_service_id_fkey FOREIGN KEY (service_id) REFERENCES public.services(id),
  CONSTRAINT tasks_workspace_id_fkey FOREIGN KEY (workspace_id) REFERENCES public.workspaces(id),
  CONSTRAINT tasks_assignee_id_fkey FOREIGN KEY (assignee_id) REFERENCES public.users(id)
);

CREATE TABLE public.tiers (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  name character varying NOT NULL,
  min_toratix integer NOT NULL,
  max_toratix integer NOT NULL,
  max_active_tasks integer NOT NULL DEFAULT 1,
  benefits jsonb DEFAULT '[]'::jsonb,
  created_at timestamp without time zone,
  max_workspaces integer NOT NULL DEFAULT 1,
  updated_at timestamp without time zone DEFAULT now(),
  CONSTRAINT tiers_pkey PRIMARY KEY (id)
);

CREATE TABLE public.token_prices (
  id integer NOT NULL DEFAULT nextval('token_prices_id_seq'::regclass),
  min_qty integer NOT NULL,
  max_qty integer NOT NULL,
  price_per_token numeric NOT NULL,
  label character varying,
  created_at timestamp without time zone DEFAULT now(),
  updated_at timestamp without time zone DEFAULT now(),
  CONSTRAINT token_prices_pkey PRIMARY KEY (id)
);

CREATE TABLE public.transactions (
    id uuid NOT NULL DEFAULT gen_random_uuid (),
    wallet_id uuid,
    type USER - DEFINED NOT NULL,
    amount integer NOT NULL,
    description text,
    reference_id uuid,
    created_at timestamp without time zone,
    updated_at timestamp without time zone DEFAULT now(),
    CONSTRAINT transactions_pkey PRIMARY KEY (id),
    CONSTRAINT transactions_wallet_id_fkey FOREIGN KEY (wallet_id) REFERENCES public.wallets (id)
);

CREATE TABLE public.users (
  id uuid NOT NULL DEFAULT gen_random_uuid(),
  email text NOT NULL UNIQUE,
  full_name text,
  avatar_url text,
  role text DEFAULT 'client'::text CHECK (role = ANY (ARRAY['client'::text, 'admin'::text, 'staff'::text])),
  created_at timestamp without time zone NOT NULL,
  password text,
  remember_token character varying,
  updated_at timestamp without time zone DEFAULT now(),
  last_login_at timestamp without time zone,
  last_login_ip character varying,
  bank_name character varying,
  bank_account character varying,
  bank_holder character varying,
  google_id text UNIQUE,
  CONSTRAINT users_pkey PRIMARY KEY (id)
);

CREATE TABLE public.wallets (
    id uuid NOT NULL DEFAULT gen_random_uuid (),
    user_id uuid UNIQUE,
    balance integer DEFAULT 0,
    total_purchased integer DEFAULT 0,
    current_tier_id bigint,
    updated_at timestamp without time zone,
    CONSTRAINT wallets_pkey PRIMARY KEY (id),
    CONSTRAINT wallets_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users (id),
    CONSTRAINT wallets_current_tier_id_fkey FOREIGN KEY (current_tier_id) REFERENCES public.tiers (id)
);

CREATE TABLE public.workspaces (
    id uuid NOT NULL DEFAULT gen_random_uuid (),
    user_id uuid NOT NULL,
    name character varying NOT NULL,
    description text,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    CONSTRAINT workspaces_pkey PRIMARY KEY (id),
    CONSTRAINT workspaces_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users (id)
);