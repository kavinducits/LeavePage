<style>
    .progress-summary-card {
        border: 0;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 0.75rem 1.75rem rgba(13, 110, 253, 0.12);
    }

    .progress-summary-card .card-header {
        padding: 1rem 1.25rem;
    }

    .progress-summary-body {
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        padding: 1.5rem;
    }

    .progress-identity-block {
        padding: 1rem 1.25rem;
        border: 1px solid #dbe7ff;
        border-radius: 0.9rem;
        background: #ffffff;
        height: 100%;
    }

    .progress-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.35rem;
    }

    .progress-primary-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0d6efd;
    }

    .progress-secondary-value {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
    }

    .progress-meta-text {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .progress-date-card {
        border: 1px solid #e5e7eb;
        border-radius: 0.9rem;
        background: #ffffff;
        padding: 1rem 1.1rem;
        height: 100%;
    }

    .progress-date-card.is-due {
        border-left: 4px solid #ffc107;
    }

    .progress-date-card.is-submitted {
        border-left: 4px solid #198754;
    }

    .progress-metric-card {
        border-radius: 0.9rem;
        background: linear-gradient(135deg, #0d6efd 0%, #3d8bfd 100%);
        color: #ffffff;
        padding: 1rem 1.1rem;
        height: 100%;
    }

    .progress-metric-card .metric-value {
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .progress-remarks-panel,
    .progress-doc-panel {
        border: 1px solid #e9ecef;
        border-radius: 0.9rem;
        background: #ffffff;
        padding: 1rem 1.1rem;
        height: 100%;
    }

    .progress-remarks-text {
        white-space: pre-wrap;
        line-height: 1.65;
        color: #343a40;
        margin-bottom: 0;
    }
</style>
