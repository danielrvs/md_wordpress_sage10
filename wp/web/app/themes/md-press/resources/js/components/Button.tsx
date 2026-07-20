import React from 'react';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'glass';
  href?: string;
  className?: string;
  children: React.ReactNode;
}

export const Button: React.FC<ButtonProps> = ({
  variant = 'primary',
  href,
  className = '',
  children,
  ...props
}) => {
  const baseClasses =
    'inline-flex items-center justify-center font-bold rounded-xl active:scale-[0.98] transition-all duration-200 text-sm cursor-pointer text-center';

  const variants = {
    primary:
      'bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 shadow-lg hover:shadow-emerald-500/20',
    secondary: 'bg-white/10 hover:bg-white/15 text-white',
    glass: 'bg-white/5 hover:bg-white/10 border border-white/10 text-white',
  };

  const variantClass = variants[variant] || variants.primary;
  const combinedClasses = `${baseClasses} ${variantClass} ${className}`;

  if (href) {
    return (
      <a
        href={href}
        className={combinedClasses}
        {...(props as any)}
      >
        {children}
      </a>
    );
  }

  return (
    <button
      className={combinedClasses}
      {...props}
    >
      {children}
    </button>
  );
};
