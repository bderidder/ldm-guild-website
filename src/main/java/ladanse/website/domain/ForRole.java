package ladanse.website.domain;

import ladanse.website.domain.master.RoleType;

import javax.persistence.*;

@Entity
@Table(name = "ForRole")
public class ForRole {
    private Long id;
    private RoleType roleType;
    private SignUp signUp;

    protected ForRole() {
    }

    public ForRole(SignUp signUp, RoleType roleType) {
        setSignUp(signUp);
        setRoleType(roleType);
    }

    @Id
    @Column(name = "id")
    @GeneratedValue(strategy=GenerationType.IDENTITY)
    public Long getId() {
        return id;
    }

    protected void setId(Long id) {
        this.id = id;
    }

    @Basic
    @Column(name = "roleType")
    @Enumerated(EnumType.STRING)
    public RoleType getRoleType() {
        return roleType;
    }

    protected void setRoleType(RoleType roleType) {
        this.roleType = roleType;
    }

    @ManyToOne
    @JoinColumn(name = "signUpId", nullable = false)
    public SignUp getSignUp() {
        return signUp;
    }

    protected void setSignUp(SignUp signup) {
        this.signUp = signup;
    }
}
