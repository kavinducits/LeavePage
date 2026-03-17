<style>
    .card-header-dark {
        background: linear-gradient(135deg, #212529 0%, #343a40 100%);
        color: white;
        border-bottom: 3px solid #0d6efd;
    }

    .extension-summary-card {
        border: 0;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 0.75rem 1.75rem rgba(13, 110, 253, 0.12);
    }

    .extension-summary-card .card-header {
        padding: 1rem 1.25rem;
    }

    .extension-summary-body {
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        padding: 1.5rem;
    }

    .extension-identity-block {
        padding: 1rem 1.25rem;
        border: 1px solid #dbe7ff;
        border-radius: 0.9rem;
        background: #ffffff;
        height: 100%;
    }

    .extension-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.35rem;
    }

    .extension-primary-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0d6efd;
    }

    .extension-secondary-value {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
    }

    .extension-meta-text {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .extension-date-card {
        border: 1px solid #e5e7eb;
        border-radius: 0.9rem;
        background: #ffffff;
        padding: 1rem 1.1rem;
        height: 100%;
    }

    .extension-date-card.is-old {
        border-left: 4px solid #dc3545;
    }

    .extension-date-card.is-new {
        border-left: 4px solid #198754;
    }

    .extension-metric-card {
        border-radius: 0.9rem;
        background: linear-gradient(135deg, #0d6efd 0%, #3d8bfd 100%);
        color: #ffffff;
        padding: 1rem 1.1rem;
        height: 100%;
    }

    .extension-metric-card .metric-value {
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .extension-payment-panel,
    .extension-reason-panel {
        border: 1px solid #e9ecef;
        border-radius: 0.9rem;
        background: #ffffff;
        padding: 1rem 1.1rem;
        height: 100%;
    }

    .extension-reason-text {
        white-space: pre-wrap;
        line-height: 1.65;
        color: #343a40;
        margin-bottom: 0;
    }
</style>
